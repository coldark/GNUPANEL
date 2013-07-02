

-- Pre upgrade
---------------------------------------------------------
--DROP DATABASE gnupanel;

--DROP ROLE gnupanel;
--DROP ROLE apache;
--DROP ROLE pdns;
--DROP ROLE postfix;
--DROP ROLE proftpd;
--DROP ROLE sdns;
---------------------------------------------------------



GRANT ALL ON DATABASE gnupanel TO gnupanel;
GRANT CONNECT ON DATABASE gnupanel TO apache;
GRANT CONNECT ON DATABASE gnupanel TO pdns;
GRANT CONNECT ON DATABASE gnupanel TO postfix;
GRANT CONNECT ON DATABASE gnupanel TO sdns;
GRANT CONNECT ON DATABASE gnupanel TO proftpd;

\connect gnupanel

GRANT SELECT ON TABLE gnupanel_apache_user TO apache;
GRANT ALL ON TABLE gnupanel_pdns_domains TO pdns;
GRANT ALL ON TABLE gnupanel_pdns_domains_nat TO pdns;
GRANT ALL ON TABLE gnupanel_pdns_records TO pdns;
GRANT ALL ON TABLE gnupanel_pdns_records_nat TO pdns;
GRANT SELECT ON TABLE gnupanel_pdns_supermasters TO pdns;
GRANT SELECT ON TABLE gnupanel_pdns_supermasters_nat TO pdns;
GRANT SELECT ON TABLE gnupanel_postfix_alias TO postfix;
GRANT SELECT ON TABLE gnupanel_postfix_autoreply TO postfix;
GRANT SELECT ON TABLE gnupanel_postfix_mailuser TO postfix;
GRANT SELECT ON TABLE gnupanel_postfix_transport TO postfix;
GRANT SELECT ON TABLE gnupanel_postfix_transport TO sdns;
GRANT SELECT ON TABLE gnupanel_postfix_transport_listas TO postfix;
GRANT SELECT ON TABLE gnupanel_postfix_virtual TO postfix;
GRANT SELECT ON TABLE gnupanel_postfix_virtual TO sdns;
GRANT SELECT ON TABLE gnupanel_proftpd_ftpgroup TO proftpd;
GRANT INSERT,SELECT,UPDATE ON TABLE gnupanel_proftpd_ftpquotalimits TO proftpd;
GRANT INSERT,SELECT,UPDATE ON TABLE gnupanel_proftpd_ftpquotatallies TO proftpd;
GRANT SELECT,UPDATE ON TABLE gnupanel_proftpd_ftpuser TO proftpd;


ALTER TABLE gnupanel_proftpd_ftpuser ALTER COLUMN uid SET DEFAULT 33;
ALTER TABLE gnupanel_proftpd_ftpuser ALTER COLUMN gid SET DEFAULT 33;

ALTER TABLE gnupanel_proftpd_ftpgroup ALTER COLUMN gid SET DEFAULT 33;

UPDATE gnupanel_proftpd_ftpuser SET uid = 33, gid = 33 ;
UPDATE gnupanel_proftpd_ftpgroup SET gid = 33;

ALTER USER gnupanel ENCRYPTED PASSWORD 'XXXXXXXXX';
ALTER USER apache ENCRYPTED PASSWORD 'XXXXXXXXX';
ALTER USER postfix ENCRYPTED PASSWORD 'XXXXXXXXX';
ALTER USER pdns ENCRYPTED PASSWORD 'XXXXXXXXX';
ALTER USER proftpd ENCRYPTED PASSWORD 'XXXXXXXXX';
ALTER USER sdns ENCRYPTED PASSWORD 'XXXXXXXXX'; 


