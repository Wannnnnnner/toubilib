-- Adminer 4.8.1 PostgreSQL 16.6 (Debian 16.6-1.pgdg120+1) dump

DROP TABLE IF EXISTS "owners";
CREATE TABLE "public"."owners" (
    "uuid" uuid NOT NULL,
    "username" character varying(255) NOT NULL,
    CONSTRAINT "owners_pkey" PRIMARY KEY ("uuid")
) WITH (oids = false);

INSERT INTO "owners" ("uuid", "username") VALUES
('bef2bb32-6886-4b68-bd9b-e050d0265b53','toto'),
('2e3920dd-a224-455b-8bfd-c9f66a7aa036','titi');

DROP TABLE IF EXISTS "userstory";
CREATE TABLE "public"."userstory" (
    "uuid" uuid NOT NULL,
    "title" character varying(255) NOT NULL,
    "description" text NOT NULL,
    "status" character varying(50) NOT NULL,
    "owner_uuid" uuid,
    CONSTRAINT "userstory_pkey" PRIMARY KEY ("uuid")
) WITH (oids = false);

INSERT INTO "userstory" ("uuid", "title", "description", "status", "owner_uuid") VALUES
('359e126f-edc8-4232-a112-dd2726c45ae0','test','us de test','TODO','bef2bb32-6886-4b68-bd9b-e050d0265b53');

ALTER TABLE ONLY "public"."userstory" ADD CONSTRAINT "userstory_owner_uuid_fkey" FOREIGN KEY (owner_uuid) REFERENCES owners(uuid) ON DELETE SET NULL NOT DEFERRABLE;

-- 2025-02-13 09:56:12.818749+00
