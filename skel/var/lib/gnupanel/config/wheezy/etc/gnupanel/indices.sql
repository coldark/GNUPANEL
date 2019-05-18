
BEGIN;

DROP INDEX gnupanel_pdns_domains_01;

DROP INDEX gnupanel_pdns_domains_01_nat;

CREATE INDEX gnupanel_pdns_domains_01 ON gnupanel_pdns_domains (name,type);

CREATE INDEX gnupanel_pdns_domains_01_nat ON gnupanel_pdns_domains_nat (name,type);

DROP INDEX gnupanel_pdns_records_01;

DROP INDEX gnupanel_pdns_records_01_nat;

CREATE INDEX gnupanel_pdns_records_01 ON gnupanel_pdns_records (type,name);

CREATE INDEX gnupanel_pdns_records_01_nat ON gnupanel_pdns_records_nat (type,name);

END;
