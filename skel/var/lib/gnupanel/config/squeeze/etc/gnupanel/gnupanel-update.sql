
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

ALTER TABLE gnupanel_proftpd_ftpquotalimits ALTER COLUMN bytes_in_avail TYPE float8 ;
ALTER TABLE gnupanel_proftpd_ftpquotalimits ALTER COLUMN bytes_out_avail TYPE float8 ;
ALTER TABLE gnupanel_proftpd_ftpquotalimits ALTER COLUMN bytes_xfer_avail TYPE float8 ;

ALTER TABLE gnupanel_proftpd_ftpquotatallies ALTER COLUMN bytes_in_used TYPE float8 ;
ALTER TABLE gnupanel_proftpd_ftpquotatallies ALTER COLUMN bytes_out_used TYPE float8 ;
ALTER TABLE gnupanel_proftpd_ftpquotatallies ALTER COLUMN bytes_xfer_used TYPE float8 ;
ALTER TABLE gnupanel_postfix_mailuser ALTER COLUMN mailquota TYPE float8 ;
ALTER TABLE gnupanel_postfix_mailuser ALTER COLUMN mailquota SET DEFAULT 10 ;


ALTER TABLE gnupanel_botones_lang ALTER COLUMN mensaje TYPE text ;
ALTER TABLE gnupanel_login_lang ALTER COLUMN mensaje TYPE text ;
ALTER TABLE gnupanel_mensajes_lang ALTER COLUMN mensaje TYPE text ;


ALTER TABLE gnupanel_apacheconf ALTER COLUMN documentroot SET DEFAULT '/usr/share/gnupanel/gnupanel';
ALTER TABLE gnupanel_apacheconf ADD COLUMN caracteres varchar(255) DEFAULT  'ISO-8859-1';

ALTER TABLE gnupanel_reseller_data ALTER COLUMN departamento TYPE varchar(255) ;
ALTER TABLE gnupanel_reseller_data ALTER COLUMN codpostal TYPE varchar(255) ;

ALTER TABLE gnupanel_usuario_data ALTER COLUMN departamento TYPE varchar(255) ;
ALTER TABLE gnupanel_usuario_data ALTER COLUMN codpostal TYPE varchar(255) ;

UPDATE gnupanel_apacheconf SET documentroot = '/usr/share/gnupanel/gnupanel' WHERE documentroot = '/var/www/sitios/default';
UPDATE gnupanel_postfix_mailuser SET mailquota = 10 WHERE mailquota = 10000000;

DELETE FROM gnupanel_temas;
SELECT setval('gnupanel_temas_id_tema_seq',1,false);
INSERT INTO gnupanel_temas (tema) VALUES ('gnupanel');
INSERT INTO gnupanel_temas (tema) VALUES ('pop');
INSERT INTO gnupanel_temas (tema) VALUES ('light-blue');
INSERT INTO gnupanel_temas (tema) VALUES ('office');
INSERT INTO gnupanel_temas (tema) VALUES ('gnupanel2');
INSERT INTO gnupanel_temas (tema) VALUES ('gnupanel3');

INSERT INTO apache_dominios_conf VALUES (4,0);

create table gnupanel_postfix_autoreply (
  address                   varchar(255) not null,
  transport                 varchar(255) default 'autoreply' NOT NULL,
  subject                   varchar(255) NOT NULL,
  mensaje                   text NULL,
  mapa_caracteres           varchar(255) NULL,
  active                    int default 1 not null,
  constraint pk_Gnupanel_postfix_autoreply primary key (address)
) ;

alter table gnupanel_postfix_autoreply add constraint gnupanel_p_m_p_a
  foreign key (address)
  references gnupanel_postfix_mailuser (address) ON UPDATE CASCADE ON DELETE CASCADE ;

GRANT SELECT ON gnupanel_postfix_autoreply TO postfix;

create table gnupanel_postfix_autoreply_cola (
  id                        serial not null,
  address                   varchar(255),
  destino                   varchar(255) default '' NOT NULL,
  constraint pk_Gnupanel_postfix_autoreply_cola primary key (id)
) ;

alter table gnupanel_postfix_autoreply_cola add constraint gnupanel_p_a_p_a_c foreign key (address) references gnupanel_postfix_autoreply (address) ON UPDATE CASCADE ON DELETE CASCADE ;

GRANT SELECT ON gnupanel_postfix_autoreply_cola TO postfix;

create table gnupanel_transferencias_historico (
  id                        serial not null,
  ano                       int not null,
  mes                       int not null,
  fecha                     timestamp default now(),
  id_dominio                int not null,
  dominio                   varchar(255) null,
  dueno                     int,
  http                      bigint default 0 not null,
  ftp                       bigint default 0 not null,
  smtp                      bigint default 0 not null,
  pop3                      bigint default 0 not null,
  total                     bigint default 0 not null,
  tope                      bigint default 0 not null,
  constraint pk_Gnupanel_transferencias_historico primary key (id)
) ;

create table gnupanel_espacio_historico (
  id                        serial not null,
  ano                       int not null,
  mes                       int not null,
  fecha                     timestamp default now(),
  id_dominio                int NOT NULL,
  dominio                   varchar(255) null,
  dueno                     int,
  ftpweb                    bigint default 0 not null,
  correo                    bigint default 0 not null,
  postgres                  bigint default 0 not null,
  mysql                     bigint default 0 not null,
  total                     bigint default 0 not null,
  tope                      bigint default 0 not null,
  constraint pk_Gnupanel_espacio_historico primary key (id)
) ;

alter table gnupanel_transferencias_historico add constraint gnupanel_t_t_h foreign key (id_dominio) references gnupanel_transferencias (id_dominio) ON UPDATE CASCADE ON DELETE CASCADE ;
alter table gnupanel_espacio_historico add constraint gnupanel_e_e_h foreign key (id_dominio) references gnupanel_espacio (id_dominio) ON UPDATE CASCADE ON DELETE CASCADE ;

ALTER TABLE gnupanel_reseller_facturas ALTER COLUMN importe TYPE double precision;
ALTER TABLE gnupanel_reseller_facturas ALTER COLUMN impuesto TYPE double precision;

ALTER TABLE gnupanel_usuarios_facturas ALTER COLUMN importe TYPE double precision;
ALTER TABLE gnupanel_usuarios_facturas ALTER COLUMN impuesto TYPE double precision;

ALTER TABLE gnupanel_reseller_planes ALTER COLUMN precio TYPE double precision;
ALTER TABLE gnupanel_usuarios_planes ALTER COLUMN precio TYPE double precision;

ALTER TABLE gnupanel_divisas_reseller ALTER COLUMN credito TYPE double precision;
ALTER TABLE pagos_reseller ALTER COLUMN importe TYPE double precision;

ALTER TABLE gnupanel_divisas_usuario ALTER COLUMN credito TYPE double precision;
ALTER TABLE pagos_usuario ALTER COLUMN importe TYPE double precision;





