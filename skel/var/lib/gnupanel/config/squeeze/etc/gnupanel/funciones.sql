
--------------------------------------------------------------------------------------------------------------
--
--GNUPanel es un programa para el control de hospedaje WEB 
--Copyright (C) 2006  Ricardo Marcelo Alvarez rmalvarezkai@gmail.com
--
--------------------------------------------------------------------------------------------------------------
--
--Este archivo es parte de GNUPanel.
--
--	GNUPanel es Software Libre; Usted puede redistribuirlo y/o modificarlo
--	bajo los términos de la GNU Licencia Pública General (GPL) tal y como ha sido
--	públicada por la Free Software Foundation; o bien la versión 2 de la Licencia,
--	o (a su opción) cualquier versión posterior.
--
--	GNUPanel se distribuye con la esperanza de que sea útil, pero SIN NINGUNA
--	GARANTÍA; tampoco las implícitas garantías de MERCANTILIDAD o ADECUACIÓN A UN
--	PROPÓSITO PARTICULAR. Consulte la GNU General Public License (GPL) para más
--	detalles.
--
--	Usted debe recibir una copia de la GNU General Public License (GPL)
--	junto con GNUPanel; si no, escriba a la Free Software Foundation Inc.
--	51 Franklin Street, 5º Piso, Boston, MA 02110-1301, USA.
--
--------------------------------------------------------------------------------------------------------------
--
--This file is part of GNUPanel.
--
--	GNUPanel is free software; you can redistribute it and/or modify
--	it under the terms of the GNU General Public License as published by
--	the Free Software Foundation; either version 2 of the License, or
--	(at your option) any later version.
--
--	GNUPanel is distributed in the hope that it will be useful,
--	but WITHOUT ANY WARRANTY; without even the implied warranty of
--	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
--	GNU General Public License for more details.
--
--	You should have received a copy of the GNU General Public License
--	along with GNUPanel; if not, write to the Free Software
--	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
--
--------------------------------------------------------------------------------------------------------------

CREATE OR REPLACE FUNCTION apache_borrar() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

INSERT INTO gnupanel_apachedesconf VALUES(old.id_subdominio,old.id_dominio,old.subdominio,old.ip,old.puerto,old.documentroot,old.serveradmin,old.redirigir,old.dominio_destino,old.es_ssl,old.php_register_globals,old.php_safe_mode,old.estado,old.active);
UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 1 ;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER inserta_apache_borrar ON gnupanel_apacheconf;
CREATE TRIGGER inserta_apache_borrar AFTER DELETE ON gnupanel_apacheconf FOR EACH ROW EXECUTE PROCEDURE apache_borrar();

CREATE OR REPLACE FUNCTION apache_insertar() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

DELETE FROM gnupanel_apachedesconf WHERE documentroot = new.documentroot;

UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 1;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER inserta_apache_insertar ON gnupanel_apacheconf;
CREATE TRIGGER inserta_apache_insertar AFTER INSERT OR UPDATE ON gnupanel_apacheconf FOR EACH ROW EXECUTE PROCEDURE apache_insertar();

CREATE OR REPLACE FUNCTION cambia_ip() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE gnupanel_usuario SET ip = new.ip_publica WHERE ip = old.ip_publica;
UPDATE gnupanel_pdns_records SET content = new.ip_publica WHERE content = old.ip_publica;

UPDATE gnupanel_pdns_records SET content = replace(content,old.ip_publica,new.ip_publica) WHERE type = 'TXT' AND content LIKE '%' || old.ip_publica || '%';
UPDATE gnupanel_pdns_records_nat SET content = replace(content,old.ip_publica,new.ip_publica) WHERE type = 'TXT' AND content LIKE '%' || old.ip_publica || '%';

IF old.usa_nat = 1 THEN
    IF new.usa_nat = 1 THEN
	UPDATE gnupanel_pdns_records_nat SET content = new.ip_privada WHERE content = old.ip_privada;
	UPDATE gnupanel_apacheconf SET ip = new.ip_privada,estado = 1 WHERE ip = old.ip_privada;
    ELSE
	UPDATE gnupanel_pdns_records_nat SET content = new.ip_publica WHERE content = old.ip_privada;
	UPDATE gnupanel_apacheconf SET ip = new.ip_publica,estado = 1 WHERE ip = old.ip_privada;
    END IF;
ELSE
    IF new.usa_nat = 1 THEN
	UPDATE gnupanel_pdns_records_nat SET content = new.ip_privada WHERE content = old.ip_publica;
	UPDATE gnupanel_apacheconf SET ip = new.ip_privada,estado = 1 WHERE ip = old.ip_publica;
    ELSE
	UPDATE gnupanel_pdns_records_nat SET content = new.ip_publica WHERE content = old.ip_publica;
	UPDATE gnupanel_apacheconf SET ip = new.ip_publica,estado = 1 WHERE ip = old.ip_publica;
    END IF;
END IF;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER updatea_cambia_ip ON gnupanel_ips_servidor;
CREATE TRIGGER updatea_cambia_ip AFTER UPDATE ON gnupanel_ips_servidor FOR EACH ROW EXECUTE PROCEDURE cambia_ip();

CREATE OR REPLACE FUNCTION saca_plan() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE gnupanel_reseller_plan SET id_plan = 0 WHERE id_plan = old.id_plan;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER updatea_saca_plan ON gnupanel_reseller_planes;
CREATE TRIGGER updatea_saca_plan AFTER DELETE ON gnupanel_reseller_planes FOR EACH ROW EXECUTE PROCEDURE saca_plan();

CREATE OR REPLACE FUNCTION updatea_plan() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE gnupanel_reseller_plan SET id_plan=new.id_plan,vigencia_plan=new.vigencia,dominios=new.dominios,subdominios=new.subdominios,dominios_parking=new.dominios_parking,espacio=new.espacio,transferencia=new.transferencia,bases_postgres=new.bases_postgres,bases_mysql=new.bases_mysql,cuentas_correo=new.cuentas_correo,listas_correo=new.listas_correo,cuentas_ftp=new.cuentas_ftp WHERE id_plan = old.id_plan;

UPDATE gnupanel_espacio SET tope = new.espacio WHERE EXISTS (SELECT * FROM gnupanel_usuario_plan WHERE gnupanel_usuario_plan.id_plan = 0 AND gnupanel_usuario_plan.id_usuario = gnupanel_espacio.id_dominio) ;
UPDATE gnupanel_transferencias SET tope = (new.transferencia * 1048576) WHERE EXISTS (SELECT * FROM gnupanel_usuario_plan WHERE gnupanel_usuario_plan.id_plan = 0 AND gnupanel_usuario_plan.id_usuario = gnupanel_transferencias.id_dominio) ;
UPDATE gnupanel_proftpd_ftpquotalimits SET bytes_in_avail = (new.transferencia * 1048576), bytes_xfer_avail = (new.espacio * 1048576) WHERE EXISTS (SELECT userid FROM gnupanel_proftpd_ftpuser WHERE gnupanel_proftpd_ftpuser.userid = gnupanel_proftpd_ftpquotalimits.name AND EXISTS (SELECT * FROM gnupanel_usuario_plan WHERE gnupanel_usuario_plan.id_usuario = gnupanel_proftpd_ftpuser.id_dominio AND gnupanel_usuario_plan.id_plan = 0));
DELETE FROM gnupanel_reseller_extras WHERE EXISTS(SELECT * FROM gnupanel_reseller_plan WHERE gnupanel_reseller_plan.id_reseller = gnupanel_reseller_extras.id_reseller AND id_plan = new.id_plan );

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER updatea_updatea_plan ON gnupanel_reseller_planes;
CREATE TRIGGER updatea_updatea_plan AFTER UPDATE ON gnupanel_reseller_planes FOR EACH ROW EXECUTE PROCEDURE updatea_plan();

CREATE OR REPLACE FUNCTION updatea_reseller() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

--UPDATE gnupanel_usuario SET usuario = new.reseller, dominio = new.dominio WHERE usuario = old.reseller AND dominio = old.dominio;

--UPDATE gnupanel_pdns_domains SET name = new.dominio WHERE name = old.dominio;
--UPDATE gnupanel_pdns_domains_nat SET name = new.dominio WHERE name = old.dominio;

--UPDATE gnupanel_pdns_records SET name = replace(name,old.dominio,new.dominio),content = replace(replace(content,old.reseller||\'@\'||old.dominio,new.reseller||\'@\'||new.dominio),\'.\'||old.dominio,\'.\'||new.dominio) WHERE domain_id = (SELECT id_usuario FROM gnupanel_usuario WHERE dominio = new.dominio);
--UPDATE gnupanel_pdns_records_nat SET name = replace(name,old.dominio,new.dominio),content = replace(replace(content,old.reseller||\'@\'||old.dominio,new.reseller||\'@\'||new.dominio),\'.\'||old.dominio,\'.\'||new.dominio) WHERE domain_id = (SELECT id_usuario FROM gnupanel_usuario WHERE dominio = new.dominio);

--UPDATE gnupanel_transferencias SET dominio = new.dominio WHERE dominio = old.dominio;
--UPDATE gnupanel_espacio SET dominio = new.dominio WHERE dominio = old.dominio;

--UPDATE gnupanel_apacheconf SET documentroot=replace(replace(documentroot,old.reseller||\'@\'|| old.dominio,new.reseller||\'@\'|| new.dominio),old.dominio,new.dominio),serveradmin=replace(serveradmin,old.reseller||\'@\'|| old.dominio,new.reseller||\'@\'|| new.dominio) WHERE serveradmin = old.reseller||\'@\'|| old.dominio;
--UPDATE gnupanel_apacheconf SET documentroot=replace(documentroot,old.reseller||\'@\'|| old.dominio,new.reseller||\'@\'|| new.dominio) WHERE documentroot LIKE \'%\'||old.dominio||\'%\';
--UPDATE gnupanel_apacheconf SET estado = 4 WHERE id_dominio = (SELECT id_usuario FROM gnupanel_usuario WHERE cliente_de = old.id_reseller);

--RAISE NOTICE \'replace(replace(homedir,/\'||old.reseller||\'@\'||old.dominio||\'/,/\'||new.reseller||\'@\'||new.dominio||\'/),/\'||old.dominio||\'/,/\'||new.dominio||\'/)\';
--RAISE NOTICE \'%@%\',old.reseller,old.dominio;
--RAISE NOTICE \'%@%\',new.reseller,new.dominio;

--UPDATE gnupanel_postfix_transport SET dominio = new.dominio WHERE dominio = old.dominio;
--UPDATE gnupanel_postfix_mailuser SET address = new.reseller||\'@\'||new.dominio,dominio = new.dominio,maildir = replace(replace(maildir,\'/\'||old.reseller||\'@\'||old.dominio||\'/\',\'/\'||new.reseller||\'@\'||new.dominio||\'/\'),\'/\'||old.dominio||\'/\',\'/\'||new.dominio||\'/\') WHERE address = old.reseller||\'@\'||old.dominio ;
--UPDATE gnupanel_postfix_mailuser SET address = replace(address,old.dominio,new.dominio),dominio = new.dominio,maildir = replace(replace(maildir,\'/\'||old.reseller||\'@\'||old.dominio||\'/\',\'/\'||new.reseller||\'@\'||new.dominio||\'/\'),\'/\'||old.dominio||\'/\',\'/\'||new.dominio||\'/\') WHERE dominio = old.dominio;
--UPDATE gnupanel_postfix_mailuser SET maildir=replace(replace(maildir,\'/\'||old.reseller||\'@\'||old.dominio||\'/\',\'/\'||new.reseller||\'@\'||new.dominio||\'/\'),\'/\'||old.dominio||\'/\',\'/\'||new.dominio||\'/\') WHERE homedir LIKE \'%/\'||old.reseller||\'@\'||old.dominio||\'/%\';
--UPDATE gnupanel_postfix_virtual SET address = new.reseller||\'@\'||new.dominio,goto = new.reseller||\'@\'||new.dominio WHERE address = old.reseller||\'@\'||old.dominio;
--UPDATE gnupanel_postfix_virtual SET address = replace(address,old.dominio,new.dominio),goto = replace(goto,old.dominio,new.dominio) WHERE address LIKE \'%@\'||old.dominio OR goto LIKE \'%@\'||old.dominio;
--UPDATE gnupanel_postfix_alias SET entra = new.reseller||\'@\'||new.dominio,dominio = new.dominio WHERE entra = old.reseller||\'@\'||old.dominio;
--UPDATE gnupanel_postfix_alias SET dominio = new.dominio WHERE dominio = old.dominio;

--UPDATE gnupanel_proftpd_ftpuser SET userid = new.reseller||\'@\'||new.dominio,dominio = new.dominio,homedir = replace(homedir,\'/\'||old.reseller||\'@\'||old.dominio,\'/\'||new.reseller||\'@\'||new.dominio) WHERE userid = old.reseller||\'@\'||old.dominio ;
--UPDATE gnupanel_proftpd_ftpuser SET userid = replace(userid,\'@\'||old.dominio,\'@\'||new.dominio),dominio = new.dominio,homedir = replace(replace(homedir,\'/\'||old.reseller||\'@\'||old.dominio||\'/\',\'/\'||new.reseller||\'@\'||new.dominio||\'/\'),\'/\'||old.dominio||\'/\',\'/\'||new.dominio||\'/\') WHERE dominio = old.dominio;
--UPDATE gnupanel_proftpd_ftpuser SET homedir = replace(homedir,\'/\'||old.reseller||\'@\'||old.dominio||\'/\',\'/\'||new.reseller||\'@\'||new.dominio||\'/\') WHERE homedir LIKE \'%/\'||old.reseller||\'@\'||old.dominio||\'/%\';
--UPDATE gnupanel_proftpd_ftpgroup SET groupname = replace(groupname,old.reseller||\'@\'||old.dominio,new.reseller||\'@\'||new.dominio), members = replace(members,old.reseller||\'@\'||old.dominio,new.reseller||\'@\'||new.dominio) WHERE groupname = old.reseller||\'@\'||old.dominio;
--UPDATE gnupanel_proftpd_ftpgroup SET groupname = replace(groupname,\'@\'||old.dominio,\'@\'||new.dominio), members = replace(members,\'@\'||old.dominio,\'@\'||new.dominio) WHERE groupname LIKE \'%@\'||old.dominio;
--UPDATE gnupanel_proftpd_ftpquotalimits SET name = new.reseller||\'@\'||new.dominio WHERE name = old.reseller||\'@\'||old.dominio;
--UPDATE gnupanel_proftpd_ftpquotalimits SET name = replace(name,\'@\'||old.dominio,\'@\'||new.dominio) WHERE name LIKE \'@\'||old.dominio;

--UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 1;

RETURN NULL;

END;
$$ LANGUAGE plpgsql;

DROP TRIGGER updatea_updatea_reseller ON gnupanel_reseller;
CREATE TRIGGER updatea_updatea_reseller AFTER UPDATE ON gnupanel_reseller FOR EACH ROW EXECUTE PROCEDURE updatea_reseller();

CREATE OR REPLACE FUNCTION updatea_apache_user() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE gnupanel_apacheconf SET estado = 1 WHERE id_apache = new.id_subdominio;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER updatea_updatea_apache_user ON gnupanel_apache_user;
CREATE TRIGGER updatea_updatea_apache_user AFTER INSERT OR UPDATE ON gnupanel_apache_user FOR EACH ROW EXECUTE PROCEDURE updatea_apache_user();

CREATE OR REPLACE FUNCTION updatea_apache_dir_prot() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE gnupanel_apacheconf SET estado = 1 WHERE id_apache = new.id_subdominio;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER updatea_updatea_apache_dir_prot ON gnupanel_apache_dir_prot;
CREATE TRIGGER updatea_updatea_apache_dir_prot AFTER INSERT OR UPDATE ON gnupanel_apache_dir_prot FOR EACH ROW EXECUTE PROCEDURE updatea_apache_dir_prot();

CREATE OR REPLACE FUNCTION updatea_apache_otras_conf() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

IF TG_OP = 'DELETE' THEN
    UPDATE gnupanel_apacheconf SET estado = 1 WHERE id_apache = old.id_subdominio;
ELSE
    UPDATE gnupanel_apacheconf SET estado = 1 WHERE id_apache = new.id_subdominio;
END IF;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER updatea_updatea_apache_otras_conf ON gnupanel_apache_otras_conf;
CREATE TRIGGER updatea_updatea_apache_otras_conf AFTER INSERT OR UPDATE OR DELETE ON gnupanel_apache_otras_conf FOR EACH ROW EXECUTE PROCEDURE updatea_apache_otras_conf();

CREATE OR REPLACE FUNCTION updatea_usuario_plan() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE gnupanel_usuario_plan SET id_plan=new.id_plan,vigencia_plan=new.vigencia,subdominios=new.subdominios,dominios_parking=new.dominios_parking,espacio=new.espacio,transferencia=new.transferencia,bases_postgres=new.bases_postgres,bases_mysql=new.bases_mysql,cuentas_correo=new.cuentas_correo,listas_correo=new.listas_correo,cuentas_ftp=new.cuentas_ftp WHERE id_plan = old.id_plan;
UPDATE gnupanel_espacio SET tope = new.espacio WHERE EXISTS (SELECT * FROM gnupanel_usuario_plan WHERE gnupanel_usuario_plan.id_plan = new.id_plan AND gnupanel_espacio.id_dominio = gnupanel_usuario_plan.id_usuario) ;
UPDATE gnupanel_transferencias SET tope = (new.transferencia * 1048576) WHERE EXISTS (SELECT * FROM gnupanel_usuario_plan WHERE gnupanel_usuario_plan.id_plan = new.id_plan AND gnupanel_transferencias.id_dominio = gnupanel_usuario_plan.id_usuario) ;
UPDATE gnupanel_proftpd_ftpquotalimits SET bytes_in_avail = (new.transferencia * 1048576), bytes_xfer_avail = (new.espacio * 1048576) WHERE EXISTS (SELECT userid FROM gnupanel_proftpd_ftpuser WHERE gnupanel_proftpd_ftpuser.userid = gnupanel_proftpd_ftpquotalimits.name AND EXISTS (SELECT * FROM gnupanel_usuario_plan WHERE gnupanel_usuario_plan.id_usuario = gnupanel_proftpd_ftpuser.id_dominio AND gnupanel_usuario_plan.id_plan = new.id_plan));

DELETE FROM gnupanel_usuario_extras WHERE EXISTS(SELECT * FROM gnupanel_usuario_plan WHERE gnupanel_usuario_plan.id_usuario = gnupanel_usuario_extras.id_usuario AND id_plan = new.id_plan );

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER updatea_updatea_usuario_plan ON gnupanel_usuarios_planes;
CREATE TRIGGER updatea_updatea_usuario_plan AFTER UPDATE ON gnupanel_usuarios_planes FOR EACH ROW EXECUTE PROCEDURE updatea_usuario_plan();


CREATE OR REPLACE FUNCTION listas_borrar() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

INSERT INTO gnupanel_postfix_listas_remover VALUES(old.id_lista,old.nombre_lista);
UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 2 ;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER inserta_listas_borrar ON gnupanel_postfix_listas;
CREATE TRIGGER inserta_listas_borrar AFTER DELETE ON gnupanel_postfix_listas FOR EACH ROW EXECUTE PROCEDURE listas_borrar();

CREATE OR REPLACE FUNCTION listas_insertar() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

DELETE FROM gnupanel_postfix_listas_remover WHERE gnupanel_postfix_listas_remover.nombre_lista = new.nombre_lista;

UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 2;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER inserta_listas_insertar ON gnupanel_postfix_listas;
CREATE TRIGGER inserta_listas_insertar AFTER INSERT ON gnupanel_postfix_listas FOR EACH ROW EXECUTE PROCEDURE listas_insertar();

CREATE OR REPLACE FUNCTION borrar_subdominio_en_pdns_nat() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

IF old.type = 'A' THEN
    DELETE FROM gnupanel_pdns_records_nat WHERE name = old.name AND type = old.type ;
ELSE
    DELETE FROM gnupanel_pdns_records_nat WHERE name = old.name AND type = old.type AND content = old.content;
END IF;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER t_borrar_subdominio_en_pdns_nat ON gnupanel_pdns_records;
CREATE TRIGGER t_borrar_subdominio_en_pdns_nat AFTER DELETE ON gnupanel_pdns_records FOR EACH ROW EXECUTE PROCEDURE borrar_subdominio_en_pdns_nat();



CREATE OR REPLACE FUNCTION informes_insertar() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 3;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER inserta_informes_insertar_admin ON gnupanel_informes_admin;
CREATE TRIGGER inserta_informes_insertar_admin AFTER INSERT ON gnupanel_informes_admin FOR EACH ROW EXECUTE PROCEDURE informes_insertar();

DROP TRIGGER inserta_informes_insertar_reseller ON gnupanel_informes_reseller;
CREATE TRIGGER inserta_informes_insertar_reseller AFTER INSERT ON gnupanel_informes_reseller FOR EACH ROW EXECUTE PROCEDURE informes_insertar();


CREATE OR REPLACE FUNCTION usuarios_insertar() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

INSERT INTO gnupanel_usuarios_dominios(id,id_usuario) VALUES (new.id_usuario,new.id_usuario);

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER usuarios_insertar_trig ON gnupanel_usuario;
CREATE TRIGGER usuarios_insertar_trig AFTER INSERT ON gnupanel_usuario FOR EACH ROW EXECUTE PROCEDURE usuarios_insertar();


CREATE OR REPLACE FUNCTION autoreply_contestar() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 3;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER autoreply ON gnupanel_postfix_autoreply_cola;
CREATE TRIGGER autoreply AFTER INSERT OR UPDATE ON gnupanel_postfix_autoreply_cola FOR EACH ROW EXECUTE PROCEDURE autoreply_contestar();


CREATE OR REPLACE FUNCTION postfix_reload() RETURNS TRIGGER AS $$

DECLARE
 
BEGIN

UPDATE apache_dominios_conf SET configurar = 1 WHERE id = 4;

RETURN NULL;

END; 
$$ LANGUAGE plpgsql; 

DROP TRIGGER postfix_reloaded ON gnupanel_postfix_autoreply;
CREATE TRIGGER postfix_reloaded AFTER INSERT OR UPDATE OR DELETE ON gnupanel_postfix_autoreply FOR EACH ROW EXECUTE PROCEDURE postfix_reload();


