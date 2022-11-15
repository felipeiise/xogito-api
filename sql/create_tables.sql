-- ----------------------------
-- Sequence structure for users_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."users_id_seq";
CREATE SEQUENCE "public"."users_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 9223372036854775807
START 1
CACHE 1;
ALTER SEQUENCE "public"."users_id_seq" OWNER TO "postgres";

-- ----------------------------
-- Sequence structure for users_roles_id_seq
-- ----------------------------
DROP SEQUENCE IF EXISTS "public"."users_roles_id_seq";
CREATE SEQUENCE "public"."users_roles_id_seq" 
INCREMENT 1
MINVALUE  1
MAXVALUE 32767
START 1
CACHE 1;
ALTER SEQUENCE "public"."users_roles_id_seq" OWNER TO "postgres";

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS "public"."roles";
CREATE TABLE "public"."roles" (
  "id" int2 NOT NULL DEFAULT nextval('users_roles_id_seq'::regclass),
  "name" varchar(255) COLLATE "pg_catalog"."default" NOT NULL
)
;
ALTER TABLE "public"."roles" OWNER TO "postgres";

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS "public"."users";
CREATE TABLE "public"."users" (
  "id" int8 NOT NULL DEFAULT nextval('users_id_seq'::regclass),
  "email" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "password" text COLLATE "pg_catalog"."default" NOT NULL,
  "full_name" varchar(255) COLLATE "pg_catalog"."default" NOT NULL,
  "mfa_secret" varchar(255) COLLATE "pg_catalog"."default",
  "status" int2 NOT NULL,
  "created_at" timestamp(6) NOT NULL,
  "updated_at" timestamp(6) NOT NULL
)
;
ALTER TABLE "public"."users" OWNER TO "postgres";

-- ----------------------------
-- Table structure for users_roles
-- ----------------------------
DROP TABLE IF EXISTS "public"."users_roles";
CREATE TABLE "public"."users_roles" (
  "user_id" int8 NOT NULL,
  "role_id" int2 NOT NULL
)
;
ALTER TABLE "public"."users_roles" OWNER TO "postgres";

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."users_id_seq"
OWNED BY "public"."users"."id";
SELECT setval('"public"."users_id_seq"', 52, true);

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."users_roles_id_seq"
OWNED BY "public"."roles"."id";
SELECT setval('"public"."users_roles_id_seq"', 2, true);

-- ----------------------------
-- Indexes structure for table roles
-- ----------------------------
CREATE UNIQUE INDEX "name" ON "public"."roles" USING btree (
  "name" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table roles
-- ----------------------------
ALTER TABLE "public"."roles" ADD CONSTRAINT "users_roles_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Indexes structure for table users
-- ----------------------------
CREATE UNIQUE INDEX "email" ON "public"."users" USING btree (
  "email" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
);

-- ----------------------------
-- Primary Key structure for table users
-- ----------------------------
ALTER TABLE "public"."users" ADD CONSTRAINT "users_pkey" PRIMARY KEY ("id");

-- ----------------------------
-- Primary Key structure for table users_roles
-- ----------------------------
ALTER TABLE "public"."users_roles" ADD CONSTRAINT "users_roles_pkey1" PRIMARY KEY ("user_id", "role_id");

-- ----------------------------
-- Foreign Keys structure for table users_roles
-- ----------------------------
ALTER TABLE "public"."users_roles" ADD CONSTRAINT "role_id" FOREIGN KEY ("role_id") REFERENCES "public"."roles" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE "public"."users_roles" ADD CONSTRAINT "user_id" FOREIGN KEY ("user_id") REFERENCES "public"."users" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION;
