-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
INSERT INTO "public"."roles" ("id", "name") VALUES (1, 'Administrator');
INSERT INTO "public"."roles" ("id", "name") VALUES (2, 'User');
COMMIT;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO "public"."users" ("id", "email", "password", "full_name", "mfa_secret",  "status", "created_at", "updated_at") VALUES (1, 'first_admin@email.com', 'fa585d89c851dd338a70dcf535aa2a92fee7836dd6aff1226583e88e0996293f16bc009c652826e0fc5c706695a03cddce372f139eff4d13959da6f1f5d3eabe', 'First Administrator', 'ZV35M4AEKWTO76W3', 1, '2022-11-14 00:23:43', '2022-11-14 00:23:43');
COMMIT;

-- ----------------------------
-- Records of users_roles
-- ----------------------------
BEGIN;
INSERT INTO "public"."users_roles" ("user_id", "role_id") VALUES (1, 1);
COMMIT;

-- ----------------------------
-- Alter sequences owned by
-- ----------------------------
ALTER SEQUENCE "public"."users_id_seq"
OWNED BY "public"."users"."id";
SELECT setval('"public"."users_id_seq"', 1, true);

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
