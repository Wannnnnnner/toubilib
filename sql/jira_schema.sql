
DROP TABLE IF EXISTS "owners";
CREATE TABLE "public"."owners" (
    uuid UUID PRIMARY KEY,
    username VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS "userstory";
CREATE TABLE "public"."userstory" (
    uuid UUID PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status VARCHAR(50) NOT NULL, 
    owner_uuid UUID,
    FOREIGN KEY (owner_uuid) REFERENCES owners(uuid) ON DELETE SET NULL
);