# Changelog

## [1.19.0](https://github.com/googleapis/gapic-generator-php/compare/v1.18.3...v1.19.0) (2025-06-26)


### Features

* Add ExplicitPagination ([#759](https://github.com/googleapis/gapic-generator-php/issues/759)) ([39952d9](https://github.com/googleapis/gapic-generator-php/commit/39952d9a042199a80dd99ae020ca90162cebf736))


### Bug Fixes

* Update php build rules ([#764](https://github.com/googleapis/gapic-generator-php/issues/764)) ([aa29f44](https://github.com/googleapis/gapic-generator-php/commit/aa29f445b8b36d3c838025e9bbfc313796705d8d))

## [1.18.3](https://github.com/googleapis/gapic-generator-php/compare/v1.18.2...v1.18.3) (2025-03-03)


### Bug Fixes

* Ensure hash is int to prevent loss of precision error ([#746](https://github.com/googleapis/gapic-generator-php/issues/746)) ([3e3d435](https://github.com/googleapis/gapic-generator-php/commit/3e3d43597904f8bfbf82410462dcab7bea34a3c4))

## [1.18.2](https://github.com/googleapis/gapic-generator-php/compare/v1.18.1...v1.18.2) (2025-01-28)


### Bug Fixes

* Ensure ChannelCredentials class exists ([#743](https://github.com/googleapis/gapic-generator-php/issues/743)) ([02b24e8](https://github.com/googleapis/gapic-generator-php/commit/02b24e8e1340958fbd93fa1b5c658f3d33eeba90))

## [1.18.1](https://github.com/googleapis/gapic-generator-php/compare/v1.18.0...v1.18.1) (2025-01-24)


### Bug Fixes

* Add emulator support for pubsub clients ([#734](https://github.com/googleapis/gapic-generator-php/issues/734)) ([652ae4b](https://github.com/googleapis/gapic-generator-php/commit/652ae4b2162c357c0d369f9551e3cab94b92b38c))

## [1.18.0](https://github.com/googleapis/gapic-generator-php/compare/v1.17.0...v1.18.0) (2024-12-06)


### Features

* Add compatibility for PHP 8.4 ([#736](https://github.com/googleapis/gapic-generator-php/issues/736)) ([0d7b5eb](https://github.com/googleapis/gapic-generator-php/commit/0d7b5ebb54a3bc805647fa1a9feb5a46a8acec91))
* Add documentation for the Logger option inside the options array ([#731](https://github.com/googleapis/gapic-generator-php/issues/731)) ([4a2bdb1](https://github.com/googleapis/gapic-generator-php/commit/4a2bdb19c7894c9cd3f3a10d11ebaaef8b781c46))

## [1.17.0](https://github.com/googleapis/gapic-generator-php/compare/v1.16.1...v1.17.0) (2024-10-30)


### Features

* Generic type for docs in async magic methods ([#732](https://github.com/googleapis/gapic-generator-php/issues/732)) ([68c88ed](https://github.com/googleapis/gapic-generator-php/commit/68c88eddcef45e100d357218e7f141d4f3b92a4d))

## [1.16.1](https://github.com/googleapis/gapic-generator-php/compare/v1.16.0...v1.16.1) (2024-08-08)


### Bug Fixes

* Revert two optional to required fields for dialogflow ([#723](https://github.com/googleapis/gapic-generator-php/issues/723)) ([307d871](https://github.com/googleapis/gapic-generator-php/commit/307d871d55240dfb99ca837a4993f4db574fa1f8))

## [1.16.0](https://github.com/googleapis/gapic-generator-php/compare/v1.15.0...v1.16.0) (2024-06-04)


### Features

* Add emulator support for Bigtable and Spanner clients ([#719](https://github.com/googleapis/gapic-generator-php/issues/719)) ([83b772b](https://github.com/googleapis/gapic-generator-php/commit/83b772b63ca454542ef4a3f07590e082f18abab8))
* Add null coalescing assign ([#720](https://github.com/googleapis/gapic-generator-php/issues/720)) ([46f8670](https://github.com/googleapis/gapic-generator-php/commit/46f86705ea170345ea77cdfc65492733e343806d))

## [1.15.0](https://github.com/googleapis/gapic-generator-php/compare/v1.14.0...v1.15.0) (2024-05-30)


### Features

* New surface custom LROs ([#712](https://github.com/googleapis/gapic-generator-php/issues/712)) ([0afff0f](https://github.com/googleapis/gapic-generator-php/commit/0afff0f74e88e5097a2be33e6004b0367ecc7b6d))
* New surface LROs for new surface clients ([#714](https://github.com/googleapis/gapic-generator-php/issues/714)) ([908d92f](https://github.com/googleapis/gapic-generator-php/commit/908d92f3ef8c0f26cffc65e8b5eb9987eba813a8))

## [1.14.0](https://github.com/googleapis/gapic-generator-php/compare/v1.13.0...v1.14.0) (2024-04-29)


### Features

* Add support for the API version header ([#706](https://github.com/googleapis/gapic-generator-php/issues/706)) ([28a3161](https://github.com/googleapis/gapic-generator-php/commit/28a31618bf50d192ce81cf8bfd5be3200152d481))

## [1.13.0](https://github.com/googleapis/gapic-generator-php/compare/v1.12.1...v1.13.0) (2024-04-25)


### Features

* Add support for grpc-only gapic clients ([#707](https://github.com/googleapis/gapic-generator-php/issues/707)) ([5783c98](https://github.com/googleapis/gapic-generator-php/commit/5783c98c13215969eb35696b17d55a00ed711b1b))

## [1.12.1](https://github.com/googleapis/gapic-generator-php/compare/v1.12.0...v1.12.1) (2024-04-15)


### Bug Fixes

* Patch optional to required changes in BigQueryDataTransfer ([#703](https://github.com/googleapis/gapic-generator-php/issues/703)) ([ccfc769](https://github.com/googleapis/gapic-generator-php/commit/ccfc769d89442d411ffa1c62f8c0f340a4407ded))

## [1.12.0](https://github.com/googleapis/gapic-generator-php/compare/v1.11.0...v1.12.0) (2024-04-12)


### Features

* Add patches for bigtable required to optional changes ([#702](https://github.com/googleapis/gapic-generator-php/issues/702)) ([207191c](https://github.com/googleapis/gapic-generator-php/commit/207191c1f3a07a24338b47735c39bf31a01794ee))


### Bug Fixes

* Add license and generated code headers to generated resource files ([#696](https://github.com/googleapis/gapic-generator-php/issues/696)) ([8409511](https://github.com/googleapis/gapic-generator-php/commit/8409511b8acc296f502694e26e1b0b8a5e52a262))

## [1.11.0](https://github.com/googleapis/gapic-generator-php/compare/v1.10.0...v1.11.0) (2024-02-28)


### Features

* Support configuring emulators from environment variable ([#687](https://github.com/googleapis/gapic-generator-php/issues/687)) ([5409e38](https://github.com/googleapis/gapic-generator-php/commit/5409e389714a511253608288de4028787611e58b))
* UUID auto-generation for fields listed for auto population in service.yaml ([#686](https://github.com/googleapis/gapic-generator-php/issues/686)) ([57bef7c](https://github.com/googleapis/gapic-generator-php/commit/57bef7c61582708f5daae6559b9e66810bfbef69))


### Bug Fixes

* Correct method for supportedTransports ([#692](https://github.com/googleapis/gapic-generator-php/issues/692)) ([e7e08da](https://github.com/googleapis/gapic-generator-php/commit/e7e08da23a4a7b013f94ab65e0855e2f26ebe946))
* Remove PhpFactory::create from PhpClassComparer ([#688](https://github.com/googleapis/gapic-generator-php/issues/688)) ([106d3ee](https://github.com/googleapis/gapic-generator-php/commit/106d3ee4038d708d8e2b3e3caa6f899180401a72))

## [1.10.0](https://github.com/googleapis/gapic-generator-php/compare/v1.9.2...v1.10.0) (2023-12-14)


### Features

* Add support for Universe Domain via service address template ([#658](https://github.com/googleapis/gapic-generator-php/issues/658)) ([26661dd](https://github.com/googleapis/gapic-generator-php/commit/26661dd0090a6edc88684d1803c619a32dae7dbe))
* Mark new surface GA and previous surface deprecated ([#674](https://github.com/googleapis/gapic-generator-php/issues/674)) ([e45bf0e](https://github.com/googleapis/gapic-generator-php/commit/e45bf0efb754581f84d08fb37364345e7dbdc794))


### Bug Fixes

* **deps:** Update dependency symplify/coding-standard to v9.4.70 ([#586](https://github.com/googleapis/gapic-generator-php/issues/586)) ([a015824](https://github.com/googleapis/gapic-generator-php/commit/a015824e4156e67ba87bac04024a0a344b085ee5))

## [1.9.2](https://github.com/googleapis/gapic-generator-php/compare/v1.9.1...v1.9.2) (2023-11-03)


### Bug Fixes

* Prevent duplicate fragment gen ([#669](https://github.com/googleapis/gapic-generator-php/issues/669)) ([b25acf0](https://github.com/googleapis/gapic-generator-php/commit/b25acf0a88daad899a7b30956d9ac3f537eb5ac6))

## [1.9.1](https://github.com/googleapis/gapic-generator-php/compare/v1.9.0...v1.9.1) (2023-10-13)


### Bug Fixes

* Phpdoc ref for async methods ([#664](https://github.com/googleapis/gapic-generator-php/issues/664)) ([9e7e19d](https://github.com/googleapis/gapic-generator-php/commit/9e7e19d53341f14f868123d661ec48fd4d5aa313))

## [1.9.0](https://github.com/googleapis/gapic-generator-php/compare/v1.8.4...v1.9.0) (2023-10-04)


### Features

* Consolidate BaseClient classes in V2 generator ([#659](https://github.com/googleapis/gapic-generator-php/issues/659)) ([67375ee](https://github.com/googleapis/gapic-generator-php/commit/67375ee09163666122564efbd4685ccfce962795))


### Bug Fixes

* **docs:** Invalid self references for async methods ([#662](https://github.com/googleapis/gapic-generator-php/issues/662)) ([8fa9a47](https://github.com/googleapis/gapic-generator-php/commit/8fa9a4788fd57fa0fa3a990920a14247c0c15ea4))

## [1.8.4](https://github.com/googleapis/gapic-generator-php/compare/v1.8.3...v1.8.4) (2023-09-15)


### Bug Fixes

* Optional-to-required CreateRepositoryRequest fields ([#656](https://github.com/googleapis/gapic-generator-php/issues/656)) ([aabc46e](https://github.com/googleapis/gapic-generator-php/commit/aabc46e1f58ef3ac2ac6810193c4b37df0992538))

## [1.8.3](https://github.com/googleapis/gapic-generator-php/compare/v1.8.2...v1.8.3) (2023-09-15)


### Bug Fixes

* Optional-to-required parameter in NetworkConnectivity policy bas… ([#653](https://github.com/googleapis/gapic-generator-php/issues/653)) ([b303efe](https://github.com/googleapis/gapic-generator-php/commit/b303efe78fae116a3b3cfd71eee295d162b24c9f))

## [1.8.2](https://github.com/googleapis/gapic-generator-php/compare/v1.8.1...v1.8.2) (2023-09-11)


### Bug Fixes

* ImportMappingRules optional-to-required parameters for DMS ([#651](https://github.com/googleapis/gapic-generator-php/issues/651)) ([c4059a9](https://github.com/googleapis/gapic-generator-php/commit/c4059a98bc13f5c0a0797a9e9798a916413dfa97))

## [1.8.1](https://github.com/googleapis/gapic-generator-php/compare/v1.8.0...v1.8.1) (2023-09-07)


### Bug Fixes

* Optional-to-required field for Cloud DMS ([#649](https://github.com/googleapis/gapic-generator-php/issues/649)) ([613baec](https://github.com/googleapis/gapic-generator-php/commit/613baec2d7c99002f734580583f52ac7cba8ee15))

## [1.8.0](https://github.com/googleapis/gapic-generator-php/compare/v1.7.6...v1.8.0) (2023-06-09)


### Features

* Add phpdoc examples tag ([#636](https://github.com/googleapis/gapic-generator-php/issues/636)) ([500e006](https://github.com/googleapis/gapic-generator-php/commit/500e006795bf97a3d70a4bd70b46bbda59aff552))
* **docs:** Add migration information to service class descriptions ([2e1f6d3](https://github.com/googleapis/gapic-generator-php/commit/2e1f6d3116ac6302e30b15f7de60f05bcd208fbe))

## [1.7.6](https://github.com/googleapis/gapic-generator-php/compare/v1.7.5...v1.7.6) (2023-05-31)


### Bug Fixes

* Include non-required resource fields in helpers ([#635](https://github.com/googleapis/gapic-generator-php/issues/635)) ([bd1be34](https://github.com/googleapis/gapic-generator-php/commit/bd1be349e6c75e6e529572f9948189dfbc1f8df1))
* Increase php memory limit ([#631](https://github.com/googleapis/gapic-generator-php/issues/631)) ([685b419](https://github.com/googleapis/gapic-generator-php/commit/685b419f2220e2d19c74e7f1464067f995cf1a95))
* Update PHP prebuilt binary, version 8.1.13 ([#634](https://github.com/googleapis/gapic-generator-php/issues/634)) ([29be36e](https://github.com/googleapis/gapic-generator-php/commit/29be36e9541a15edbee4dd037489de41ca6f7180))

## [1.7.5](https://github.com/googleapis/gapic-generator-php/compare/v1.7.4...v1.7.5) (2023-05-10)


### Bug Fixes

* Remove oneof wrappers in v2 ([#629](https://github.com/googleapis/gapic-generator-php/issues/629)) ([f69fcfb](https://github.com/googleapis/gapic-generator-php/commit/f69fcfb19121d1bb4f4c6282be3cced945e6accc))

## [1.7.4](https://github.com/googleapis/gapic-generator-php/compare/v1.7.3...v1.7.4) (2023-05-09)


### Bug Fixes

* Do not generate fragments for requests in other packages ([#626](https://github.com/googleapis/gapic-generator-php/issues/626)) ([d5be067](https://github.com/googleapis/gapic-generator-php/commit/d5be067dce236efb8f66737e80074277dc5dfd86))

## [1.7.3](https://github.com/googleapis/gapic-generator-php/compare/v1.7.2...v1.7.3) (2023-05-03)


### Bug Fixes

* Reduce noise by reverting sample comment ([#622](https://github.com/googleapis/gapic-generator-php/issues/622)) ([d2e87ab](https://github.com/googleapis/gapic-generator-php/commit/d2e87ab51912c4f1e3e09e85f11330a1d3ef7970))

## [1.7.2](https://github.com/googleapis/gapic-generator-php/compare/v1.7.1...v1.7.2) (2023-05-02)


### Bug Fixes

* **bazel:** Ignore empty zip files during packaging ([#619](https://github.com/googleapis/gapic-generator-php/issues/619)) ([0db730f](https://github.com/googleapis/gapic-generator-php/commit/0db730faadf82ffc1430ef2cc42c3919e48fa91d))

## [1.7.1](https://github.com/googleapis/gapic-generator-php/compare/v1.7.0...v1.7.1) (2023-05-02)


### Bug Fixes

* Lower hashing constant to avoid overflow ([#617](https://github.com/googleapis/gapic-generator-php/issues/617)) ([b05d16f](https://github.com/googleapis/gapic-generator-php/commit/b05d16fe1650cd92a97eb2d872dc0561462924c8))

## [1.7.0](https://github.com/googleapis/gapic-generator-php/compare/v1.6.8...v1.7.0) (2023-05-01)


### Features

* Introduce next generation PHP client surface ([#612](https://github.com/googleapis/gapic-generator-php/issues/612)) ([c675c3d](https://github.com/googleapis/gapic-generator-php/commit/c675c3d6a98e37c4cb79bbcb187a481d3cbceea7))

## [1.6.8](https://github.com/googleapis/gapic-generator-php/compare/v1.6.7...v1.6.8) (2023-04-26)


### Bug Fixes

* **tts:** Prevent TTS opt to req breakage ([#613](https://github.com/googleapis/gapic-generator-php/issues/613)) ([54d6378](https://github.com/googleapis/gapic-generator-php/commit/54d637815f992d9a7c199ae9f4b70742495ae3d7))

## [1.6.7](https://github.com/googleapis/gapic-generator-php/compare/v1.6.6...v1.6.7) (2023-03-07)


### Bug Fixes

* Dupe method names from mixins ([#584](https://github.com/googleapis/gapic-generator-php/issues/584)) ([378b2d4](https://github.com/googleapis/gapic-generator-php/commit/378b2d49946f0a3c171c92bb2943eaae5476a4db))
* Max precision for float test values ([#585](https://github.com/googleapis/gapic-generator-php/issues/585)) ([e4619b2](https://github.com/googleapis/gapic-generator-php/commit/e4619b2421e681b50309d7a63d8e1850e62f4df1))
* Required to optional field in Firestore ListDocuments ([996dc49](https://github.com/googleapis/gapic-generator-php/commit/996dc498a726ae634be833f0ff0b4e59cacf6dd7))
* Required-to-optional for Scheduler.UpdateJob ([#583](https://github.com/googleapis/gapic-generator-php/issues/583)) ([ca9b53a](https://github.com/googleapis/gapic-generator-php/commit/ca9b53a8b80c3de940a39bdf0d4f7493a30b0fa8))
* Required-to-optional for Spanner.CommitRequest ([#589](https://github.com/googleapis/gapic-generator-php/issues/589)) ([f5d5da7](https://github.com/googleapis/gapic-generator-php/commit/f5d5da72d0ff5d5b19b26b4b8cba2530113178a0))

## [1.6.6](https://github.com/googleapis/gapic-generator-php/compare/v1.6.5...v1.6.6) (2023-02-22)


### Bug Fixes

* Required to optional field in PubSub API ([#578](https://github.com/googleapis/gapic-generator-php/issues/578)) ([8fe38e7](https://github.com/googleapis/gapic-generator-php/commit/8fe38e7c89ac7d465ff03ef64fd8ef888bd55130))
* Use camelName for optionalArgs when settings requestParamHeaders ([#574](https://github.com/googleapis/gapic-generator-php/issues/574)) ([27a8a0f](https://github.com/googleapis/gapic-generator-php/commit/27a8a0f52ee1f29641d8123f365b1ebc1de1535f))

## [1.6.5](https://github.com/googleapis/gapic-generator-php/compare/v1.6.4...v1.6.5) (2023-01-31)


### Bug Fixes

* Protect artifact registry fields from bc ([#566](https://github.com/googleapis/gapic-generator-php/issues/566)) ([4747c98](https://github.com/googleapis/gapic-generator-php/commit/4747c984ba0cef8b2eacbd26900fbe34c2f6c543))

## [1.6.4](https://github.com/googleapis/gapic-generator-php/compare/v1.6.3...v1.6.4) (2023-01-05)


### Bug Fixes

* Properly parse recently added flags in bazel ([#556](https://github.com/googleapis/gapic-generator-php/issues/556)) ([d4469b2](https://github.com/googleapis/gapic-generator-php/commit/d4469b250a737e7ceadc9fe9e2e740364b4ceff8))

## [1.6.3](https://github.com/googleapis/gapic-generator-php/compare/v1.6.2...v1.6.3) (2022-12-14)


### Bug Fixes

* **snippetgen:** Fix field name typo ([#548](https://github.com/googleapis/gapic-generator-php/issues/548)) ([b94c362](https://github.com/googleapis/gapic-generator-php/commit/b94c36297d4f1cf82af223321b5229785eb0a8cc))

## [1.6.2](https://github.com/googleapis/gapic-generator-php/compare/v1.6.1...v1.6.2) (2022-12-05)


### Bug Fixes

* Miscellaneous snippet gen fixes ([#540](https://github.com/googleapis/gapic-generator-php/issues/540)) ([456557a](https://github.com/googleapis/gapic-generator-php/commit/456557ab8341a9e3b83df4bc13bd44f9ca6676d6))

## [1.6.1](https://github.com/googleapis/gapic-generator-php/compare/v1.6.0...v1.6.1) (2022-11-15)


### Bug Fixes

* Remove no longer required formatting of imports in tests ([#532](https://github.com/googleapis/gapic-generator-php/issues/532)) ([df2f4d6](https://github.com/googleapis/gapic-generator-php/commit/df2f4d67e32ae2c38531bcf94fabf11be455079c))

## [1.6.0](https://github.com/googleapis/gapic-generator-php/compare/v1.5.0...v1.6.0) (2022-10-27)


### Features

* Add flag for whether snippets should be generated ([#530](https://github.com/googleapis/gapic-generator-php/issues/530)) ([bea12ba](https://github.com/googleapis/gapic-generator-php/commit/bea12ba9bed276cfd1e6b11684a97617f63a7fb6))
* Introduce snippet gen support ([#483](https://github.com/googleapis/gapic-generator-php/issues/483)) ([0a52d7e](https://github.com/googleapis/gapic-generator-php/commit/0a52d7e00d41e2f1aaa165ad609878639fd5a4bc))


### Bug Fixes

* Continue to support php 7.4 ([#502](https://github.com/googleapis/gapic-generator-php/issues/502)) ([2bda6f2](https://github.com/googleapis/gapic-generator-php/commit/2bda6f298f7fcbe8f160e391b58e1ab26c045267))
* Ensure example values for repeated enums use the singular type ([#529](https://github.com/googleapis/gapic-generator-php/issues/529)) ([1e63d04](https://github.com/googleapis/gapic-generator-php/commit/1e63d04d5e2f0eb905f8ae12d6013d1ce2d2d8fd))

## [1.5.0](https://github.com/googleapis/gapic-generator-php/compare/v1.4.8...v1.5.0) (2022-07-22)


### Features

* enable generation of LRO client ([#493](https://github.com/googleapis/gapic-generator-php/issues/493)) ([1246424](https://github.com/googleapis/gapic-generator-php/commit/1246424df7af96d7e81533c8e2c623527f2e7acb))
* numeric enum option generation ([#497](https://github.com/googleapis/gapic-generator-php/issues/497)) ([b123f42](https://github.com/googleapis/gapic-generator-php/commit/b123f42e623fb6728d4be227e9468280faa71ffc))


### Bug Fixes

* **bazel:** support changing the generator ([#494](https://github.com/googleapis/gapic-generator-php/issues/494)) ([ea6fe97](https://github.com/googleapis/gapic-generator-php/commit/ea6fe97bedb9d7347877c790a9d8710d80be5774))
* RetrySettings reference ([#489](https://github.com/googleapis/gapic-generator-php/issues/489)) ([db8a2f3](https://github.com/googleapis/gapic-generator-php/commit/db8a2f3185e15886698cf0d5c07d6ae94719287c))
* update PHP prebuilt binary, version 7.4.15 ([#492](https://github.com/googleapis/gapic-generator-php/issues/492)) ([b85cc04](https://github.com/googleapis/gapic-generator-php/commit/b85cc0444a7bfe9b0553527fc9837d16d7c8f9aa))

## [1.4.8](https://github.com/googleapis/gapic-generator-php/compare/v1.4.7...v1.4.8) (2022-06-15)


### Miscellaneous Chores

* **deps:** update dependency com_google_protobuf to v3.20.1 ([#479](https://github.com/googleapis/gapic-generator-php/issues/479)) ([0d7ddf8](https://github.com/googleapis/gapic-generator-php/commit/0d7ddf80a09907b5e670ac820f36d7e5bb38eb64))
* **deps:** update dependency com_google_protobuf to v3.21.0 ([#481](https://github.com/googleapis/gapic-generator-php/issues/481)) ([926b3f5](https://github.com/googleapis/gapic-generator-php/commit/926b3f51d8552f6420cc78f56187fed4abb8ee40))
* **deps:** update dependency com_google_protobuf to v3.21.1 ([#482](https://github.com/googleapis/gapic-generator-php/issues/482)) ([541a023](https://github.com/googleapis/gapic-generator-php/commit/541a02349941ef5b9cc2943dbaf7b89b73b0a6c4))
* **deps:** update rules_proto digest to dcf9e47 ([#484](https://github.com/googleapis/gapic-generator-php/issues/484)) ([1bd677d](https://github.com/googleapis/gapic-generator-php/commit/1bd677d0008b3c24cfbd9ecce3092175176e1331))
* rename client to gapicClient in generated tests ([#486](https://github.com/googleapis/gapic-generator-php/issues/486)) ([86d1d7b](https://github.com/googleapis/gapic-generator-php/commit/86d1d7b06f1170b0e0a4c00f41dcc85c6dcf0480))

### [1.4.7](https://github.com/googleapis/gapic-generator-php/compare/v1.4.6...v1.4.7) (2022-04-20)


### Bug Fixes

* better fix for ignoring required synthetic oneofs ([#478](https://github.com/googleapis/gapic-generator-php/issues/478)) ([b337162](https://github.com/googleapis/gapic-generator-php/commit/b3371621819908e786078ade6eba0dafc2ef8d7a))
* ignore synthetic oneof from proto3_optional ([#476](https://github.com/googleapis/gapic-generator-php/issues/476)) ([bac6748](https://github.com/googleapis/gapic-generator-php/commit/bac6748e63c9aea0256f3ddd710ce87e6d71dd1e))

### [1.4.6](https://github.com/googleapis/gapic-generator-php/compare/v1.4.5...v1.4.6) (2022-02-23)


### Bug Fixes

* **bazel:** configure php with curl if rebuilding ([#463](https://github.com/googleapis/gapic-generator-php/issues/463)) ([d4a3745](https://github.com/googleapis/gapic-generator-php/commit/d4a37455bbf41e4679a6e1ea4822175ef9ccc85f))
* update PHP prebuilt binary, version 7.4.15 ([#465](https://github.com/googleapis/gapic-generator-php/issues/465)) ([387481a](https://github.com/googleapis/gapic-generator-php/commit/387481aa417c9a455629d331283d9e07df33de06))

### [1.4.5](https://github.com/googleapis/gapic-generator-php/compare/v1.4.4...v1.4.5) (2022-02-18)


### Bug Fixes

* spanner optional-to-required message ([#459](https://github.com/googleapis/gapic-generator-php/issues/459)) ([e64bab7](https://github.com/googleapis/gapic-generator-php/commit/e64bab7b2e671a5fbbfda8e9b96b9e99a00d3d0a))

### [1.4.4](https://github.com/googleapis/gapic-generator-php/compare/v1.4.3...v1.4.4) (2022-02-15)


### Bug Fixes

* protect against diregapic enum using reserved words ([#454](https://github.com/googleapis/gapic-generator-php/issues/454)) ([942623d](https://github.com/googleapis/gapic-generator-php/commit/942623d7e5af4927b4b6d0f1c32a6cf889e85212))
* update packaged version of composer and php for bazel ([#456](https://github.com/googleapis/gapic-generator-php/issues/456)) ([c1d4b6d](https://github.com/googleapis/gapic-generator-php/commit/c1d4b6da2c1d55b4be34fc44869156d5c3184307))

### [1.4.3](https://github.com/googleapis/gapic-generator-php/compare/v1.4.2...v1.4.3) (2022-02-02)


### Bug Fixes

* more firestore required-to-optional patches ([#451](https://github.com/googleapis/gapic-generator-php/issues/451)) ([e7fe5c7](https://github.com/googleapis/gapic-generator-php/commit/e7fe5c787eb23fb049e2bc27805b930319043468))

### [1.4.2](https://github.com/googleapis/gapic-generator-php/compare/v1.4.1...v1.4.2) (2022-01-31)


### Bug Fixes

* typo in required-to-optional parameter ([#449](https://github.com/googleapis/gapic-generator-php/issues/449)) ([a9578a9](https://github.com/googleapis/gapic-generator-php/commit/a9578a9ddd3780e95ed10c1fd48cd1995ff39459))

### [1.4.1](https://github.com/googleapis/gapic-generator-php/compare/v1.4.0...v1.4.1) (2022-01-31)


### Bug Fixes

* optional-to-required-parameters for Bigtable ([#446](https://github.com/googleapis/gapic-generator-php/issues/446)) ([a15d1f6](https://github.com/googleapis/gapic-generator-php/commit/a15d1f6dc3f13f4f76e0bf22c1bec9cfd435e4f1))
* required-to-optional and optional-to-required parameters for Firestore ([#447](https://github.com/googleapis/gapic-generator-php/issues/447)) ([b8246a3](https://github.com/googleapis/gapic-generator-php/commit/b8246a3db390ba4b047e285cd1625ffc1325f2ba))

## [1.4.0](https://github.com/googleapis/gapic-generator-php/compare/v1.3.1...v1.4.0) (2022-01-20)


### Features

* add manual fix for bc-breaking changes ([#439](https://github.com/googleapis/gapic-generator-php/issues/439)) ([e62b210](https://github.com/googleapis/gapic-generator-php/commit/e62b2103f9b3ff47e2c2fecb792d49c291b92c02))
* add RoutingRule explicit header injection support ([#403](https://github.com/googleapis/gapic-generator-php/issues/403)) ([bdc6987](https://github.com/googleapis/gapic-generator-php/commit/bdc6987128e88b368139fd006b9c30caef79ad14))

### [1.3.1](https://www.github.com/googleapis/gapic-generator-php/compare/v1.3.0...v1.3.1) (2022-01-10)


### Bug Fixes

* **gapic:** only wrap top-level required oneof in tests ([#436](https://www.github.com/googleapis/gapic-generator-php/issues/436)) ([2f7ef71](https://www.github.com/googleapis/gapic-generator-php/commit/2f7ef7113e36785179b64989f7bff7a9ef9c04e1))

## [1.3.0](https://www.github.com/googleapis/gapic-generator-php/compare/v1.2.1...v1.3.0) (2021-12-13)


### Features

* **diregapic:** generate string constants for enum names ([#423](https://www.github.com/googleapis/gapic-generator-php/issues/423)) ([3eb196d](https://www.github.com/googleapis/gapic-generator-php/commit/3eb196d4da38a8ea15a4055b65542c4d7f076f3a))


### Bug Fixes

* **bazel:** update composer to version 2.1.14 from 2.1.5 ([#428](https://www.github.com/googleapis/gapic-generator-php/issues/428)) ([eca03d4](https://www.github.com/googleapis/gapic-generator-php/commit/eca03d4d95ef95b973fbd6185feab9e9a0cc6673))

### [1.2.1](https://www.github.com/googleapis/gapic-generator-php/compare/v1.2.0...v1.2.1) (2021-12-08)


### Bug Fixes

* **diregapic:** ensure operation field descriptor ordering ([#426](https://www.github.com/googleapis/gapic-generator-php/issues/426)) ([dcd1c83](https://www.github.com/googleapis/gapic-generator-php/commit/dcd1c8351f15968d73eee72519a65e2f397f368c))
* **diregapic:** include additional args if op field is required ([#425](https://www.github.com/googleapis/gapic-generator-php/issues/425)) ([f0e8f60](https://www.github.com/googleapis/gapic-generator-php/commit/f0e8f60ea48e9f074c6d60c3d1357393268ad79d))
* **diregapic:** use camelCase field name in custom op test ([#424](https://www.github.com/googleapis/gapic-generator-php/issues/424)) ([b89c28b](https://www.github.com/googleapis/gapic-generator-php/commit/b89c28bfe2fd9cccae2e64c9034610cd7cc7f8cd))
* link in php docs ([#418](https://www.github.com/googleapis/gapic-generator-php/issues/418)) ([a0bf471](https://www.github.com/googleapis/gapic-generator-php/commit/a0bf471bdc6542e9a3cbde1374ade4357fabae3b))

## [1.2.0](https://www.github.com/googleapis/gapic-generator-php/compare/v1.1.1...v1.2.0) (2021-11-30)


### Features

* include server streaming in rest descriptor ([#414](https://www.github.com/googleapis/gapic-generator-php/issues/414)) ([f5f6060](https://www.github.com/googleapis/gapic-generator-php/commit/f5f6060a7c8ce8f24b6174b71a9145d96e1aefc1))

### [1.1.1](https://www.github.com/googleapis/gapic-generator-php/compare/v1.1.0...v1.1.1) (2021-11-17)


### Bug Fixes

* only expect required request fields on op ([#413](https://www.github.com/googleapis/gapic-generator-php/issues/413)) ([55dce26](https://www.github.com/googleapis/gapic-generator-php/commit/55dce261b34b110e9e497d9d01ecc0e619683715))
* remove extraneous message in custom lro tests ([#410](https://www.github.com/googleapis/gapic-generator-php/issues/410)) ([77346d4](https://www.github.com/googleapis/gapic-generator-php/commit/77346d49b0ca64e288f62316b0aa99b411a6dd17))

## [1.1.0](https://www.github.com/googleapis/gapic-generator-php/compare/v1.0.5...v1.1.0) (2021-11-10)


### Features

* add Custom Operation support for DIREGAPIC ([#386](https://www.github.com/googleapis/gapic-generator-php/issues/386)) ([f0e87da](https://www.github.com/googleapis/gapic-generator-php/commit/f0e87da337095f91ce82a082eef154712512f460))


### Bug Fixes

* **bazel:** fix macos support for bazel build ([#377](https://www.github.com/googleapis/gapic-generator-php/issues/377)) ([ed0badb](https://www.github.com/googleapis/gapic-generator-php/commit/ed0badb9672fb902798d20d7210cfa34bd2b8274))
* catalog proto service to proto file ([#399](https://www.github.com/googleapis/gapic-generator-php/issues/399)) ([e2361e8](https://www.github.com/googleapis/gapic-generator-php/commit/e2361e8c3f7171e63effcefbaa50c455d5fb512d))
* catalog services in ProtoCatalog ([#383](https://www.github.com/googleapis/gapic-generator-php/issues/383)) ([54cb96a](https://www.github.com/googleapis/gapic-generator-php/commit/54cb96af367aa74f192fd54f02918e5cb10126d1))
* **deps:** update dependency google/protobuf to v3.18.0 ([#385](https://www.github.com/googleapis/gapic-generator-php/issues/385)) ([cc1458d](https://www.github.com/googleapis/gapic-generator-php/commit/cc1458d25cf0579f1205bd9ef75e97dc4b70843e))
* enable ignore_unknown in config file parsers ([#390](https://www.github.com/googleapis/gapic-generator-php/issues/390)) ([30f7a35](https://www.github.com/googleapis/gapic-generator-php/commit/30f7a35ffc33b8ac9e08ae99220481234476984a))

### [1.0.5](https://www.github.com/googleapis/gapic-generator-php/compare/v1.0.4...v1.0.5) (2021-08-04)


### Features

* **diregapic:** serialize required query params for gax-php ([#351](https://www.github.com/googleapis/gapic-generator-php/issues/351)) ([2d5ef58](https://www.github.com/googleapis/gapic-generator-php/commit/2d5ef5853544d58de2b6fdd5cb8335c38174af23))


### Miscellaneous Chores

* release 1.0.5 ([5a34bd1](https://www.github.com/googleapis/gapic-generator-php/commit/5a34bd19452ad583e7334807291f0358c084ab3c))

### [1.0.4](https://www.github.com/googleapis/gapic-generator-php/compare/v1.0.3...v1.0.4) (2021-07-30)


### Features

* **oneofs:** Add support for required oneofs in PHP ([#336](https://www.github.com/googleapis/gapic-generator-php/issues/336)) ([493c9d7](https://www.github.com/googleapis/gapic-generator-php/commit/493c9d73a3a4fbbf10148f0e9ece469960bb2502))


### [1.0.3](https://www.github.com/googleapis/gapic-generator-php/compare/v1.0.2...v1.0.3) (2021-07-29)

### Features

* add mtls clientCertSource client option to docs ([#321](https://www.github.com/googleapis/gapic-generator-php/issues/321)) ([0b719a7](https://www.github.com/googleapis/gapic-generator-php/commit/0b719a70b5acdc5ebd0c63d7dd121d7773564b9f))


### Bug Fixes

* **bazel:** Eradicate monolith Bazel deps from PHP µgen repo ([#311](https://www.github.com/googleapis/gapic-generator-php/issues/311)) ([6c97315](https://www.github.com/googleapis/gapic-generator-php/commit/6c9731527748ca97ce07de9e5eb994b95df6bc22))
* **build:** Forego errors when clearing Bazel cache ([#317](https://www.github.com/googleapis/gapic-generator-php/issues/317)) ([f91679e](https://www.github.com/googleapis/gapic-generator-php/commit/f91679ee9eb335a3f9aa444a5f56bdb98f127347))
* **metadata:** Indicate devrel dependency on package parsing logic ([#314](https://www.github.com/googleapis/gapic-generator-php/issues/314)) ([1925d1b](https://www.github.com/googleapis/gapic-generator-php/commit/1925d1b846753b8a5706e3e9470e970871541bf5))
* put useJwtAccessWithScope in credentialsConfig ([#316](https://www.github.com/googleapis/gapic-generator-php/issues/316)) ([303bd38](https://www.github.com/googleapis/gapic-generator-php/commit/303bd38ad34a314e34e7318dc08bc81e46e874ee))


### [1.0.2](https://www.github.com/googleapis/gapic-generator-php/compare/v1.0.1...v1.0.2) (2021-06-22)


### Features

* implement jwt access option ([#309](https://www.github.com/googleapis/gapic-generator-php/issues/309)) ([b0a157a](https://www.github.com/googleapis/gapic-generator-php/commit/b0a157aa32e41a8c48ab23aaaa6dec186b759123))



### [1.0.1](https://www.github.com/googleapis/gapic-generator-php/compare/v1.0.0...v1.0.1) (2021-06-17)


### Bug Fixes

* **bazel:** Remove monolith rule deps from the PHP µgen Bazel rules ([#307](https://www.github.com/googleapis/gapic-generator-php/issues/307)) ([2cfff41](https://www.github.com/googleapis/gapic-generator-php/commit/2cfff416161db566935822959008488c8fc392ff))

## [1.0.0](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.7...v1.0.0) (2021-06-07)


### Miscellaneous Chores

* update formatting method description ([0eb9fcb](https://github.com/googleapis/gapic-generator-php/commit/0eb9fcb0194c3c1e3588f4687d8c781ea8aee23b))

### [0.1.7](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.6...v0.1.7) (2021-06-02)


### Bug Fixes

* **resnames:** Support wildcard-typed (child) resource references ([#299](https://www.github.com/googleapis/gapic-generator-php/issues/299)) ([edbd1a1](https://www.github.com/googleapis/gapic-generator-php/commit/edbd1a15fe53e0439f6a11d5d1fc94602007ec00))

### [0.1.6](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.5...v0.1.6) (2021-05-20)


### Bug Fixes

* enable standalone mixin API client generation ([#275](https://www.github.com/googleapis/gapic-generator-php/issues/275)) ([1cf61d7](https://www.github.com/googleapis/gapic-generator-php/commit/1cf61d7dd6a96a50692ef4b2f48114ed5260f742))
* **formatter:** Improve formatter error messages ([#296](https://www.github.com/googleapis/gapic-generator-php/issues/296)) ([98ae8ee](https://www.github.com/googleapis/gapic-generator-php/commit/98ae8eeffd93cfe634e6b1cb2310d1d53911e15f))
* Handle dashes in camelCase string conversion ([#297](https://www.github.com/googleapis/gapic-generator-php/issues/297)) ([7ac4013](https://www.github.com/googleapis/gapic-generator-php/commit/7ac40138c69fdbf9cb1924b5ac0f827d33ef51c2))
* **headers:** Handle subfields in routing headers ([#293](https://www.github.com/googleapis/gapic-generator-php/issues/293)) ([29e82bb](https://www.github.com/googleapis/gapic-generator-php/commit/29e82bb53d1a60ef8931340335badef94bc44e34))
* **regapic:** Remove PHP 5 workaround for list_ method renaming ([#292](https://www.github.com/googleapis/gapic-generator-php/issues/292)) ([0b242a2](https://www.github.com/googleapis/gapic-generator-php/commit/0b242a2086e47572526258982c526e25706c345f))
* **tests:** use string keys in tests with map fields ([#295](https://www.github.com/googleapis/gapic-generator-php/issues/295)) ([98bff65](https://www.github.com/googleapis/gapic-generator-php/commit/98bff65cf5f2d164044852b2d23e42e984723e75))

### [0.1.5](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.4...v0.1.5) (2021-05-07)


### Bug Fixes

* **codegen:** support proto map fields in tests ([#290](https://www.github.com/googleapis/gapic-generator-php/issues/290)) ([ee117a2](https://www.github.com/googleapis/gapic-generator-php/commit/ee117a2665971e99d80f859a625c4a8548bb5f62))
* disable strict_types for PHP 5 compatibility ([#286](https://www.github.com/googleapis/gapic-generator-php/issues/286)) ([aa3e385](https://www.github.com/googleapis/gapic-generator-php/commit/aa3e385b036c38f11a43cb89684cdd22fc7568d3))
* **regapic:** Prefer map over repeated fields for pagination ([#287](https://www.github.com/googleapis/gapic-generator-php/issues/287)) ([9270745](https://www.github.com/googleapis/gapic-generator-php/commit/9270745e174132a21a735bce3f6d9ef38232014b))
* **regapic:** use key-value loop iteration for pagination map resources ([#284](https://www.github.com/googleapis/gapic-generator-php/issues/284)) ([4f0aa8f](https://www.github.com/googleapis/gapic-generator-php/commit/4f0aa8f86df39e54578b7713c2dbe1baa7062b7f))
* **regapic:** use list_ RPC name for PHP 7.2 compatibility ([#285](https://www.github.com/googleapis/gapic-generator-php/issues/285)) ([adecff8](https://www.github.com/googleapis/gapic-generator-php/commit/adecff87386b8f9a7f1798c32ac7ee0fd8ddf0f2))

### [0.1.4](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.3...v0.1.4) (2021-05-06)


### Bug Fixes

* Replace PHP prebuilt binary with the one working on workstations and kokoro build image ([#280](https://www.github.com/googleapis/gapic-generator-php/issues/280)) ([f42295b](https://www.github.com/googleapis/gapic-generator-php/commit/f42295b4dbc38cfc894935d3a4b3b58d82931b03))

### [0.1.3](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.2...v0.1.3) (2021-05-06)


### Features

* support REGAPIC pagination, add compute_small goldens ([#278](https://www.github.com/googleapis/gapic-generator-php/issues/278)) ([81efb9f](https://www.github.com/googleapis/gapic-generator-php/commit/81efb9f7b71047d0ec097b604226c829f3e0ea99))


### Bug Fixes

* **diregapic:** pipe transport flag through Bazel, add Bazel REGAPIC integration test ([#276](https://www.github.com/googleapis/gapic-generator-php/issues/276)) ([832a42f](https://www.github.com/googleapis/gapic-generator-php/commit/832a42f5e9ef454852005d743c5b40fc30b0fd61))


### [0.1.2](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.1...v0.1.2) (2021-05-05)

### Bug Fixes
* Improve Map missing key error messages ([61b9f07](https://github.com/googleapis/gapic-generator-php/commit/61b9f07481f57aa8c5011a8f6e58e436c607ae2c))

### Features
* **diregapic:** Add PHP client library DIREGAPIC support ([66e60cb](https://github.com/googleapis/gapic-generator-php/commit/66e60cb36e873692249e448ddbcba1ae86c9281a))


### [0.1.1](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.0...v0.1.1) (2021-04-27)


### Bug Fixes

* Handle wildcard patterns for resname field init values ([f1c1a8f](https://www.github.com/googleapis/gapic-generator-php/commit/f1c1a8f27068d4d9e9ec815a07b3cd4d06e67a0e))
* improve Map duplicate-key error message ([c040d5b](https://www.github.com/googleapis/gapic-generator-php/commit/c040d5b1fc82effabf11264837c084d48aae126a))
* improve map missing key error msg ([61b9f07](https://www.github.com/googleapis/gapic-generator-php/commit/61b9f07481f57aa8c5011a8f6e58e436c607ae2c))
* improve Map non-existent key error message ([0df2320](https://www.github.com/googleapis/gapic-generator-php/commit/0df23203e216b9096dd9ba2c25af02d1d8b1ba1a))
* Prevent wildcard resnames from using fooName() for init vals ([be096aa](https://www.github.com/googleapis/gapic-generator-php/commit/be096aab6d040882676b5a0ea0acb88065d247a1))

## [0.1.0](https://www.github.com/googleapis/gapic-generator-php/compare/v0.0.7...v0.1.0) (2021-04-09)


### Features

* **codegen:** Enable strict types in generated code ([8fc290e](https://www.github.com/googleapis/gapic-generator-php/commit/8fc290e314236b0b2f3fc16bc8b29ba7bbe98cd2))
* **codegen:** propagate protobuf 'deprecated' to classes/methods ([9636748](https://www.github.com/googleapis/gapic-generator-php/commit/96367485fab50cd795e84606d5197e75851d9883))


### Bug Fixes

* **codegen:** Update autogen comment warnings with microgenerator references ([eac47d5](https://www.github.com/googleapis/gapic-generator-php/commit/eac47d54086a24ab2af8b27d5b83576d6a915983))

### [0.0.7](https://www.github.com/googleapis/gapic-generator-php/compare/v0.0.6...v0.0.7) (2021-04-06)


### Bug Fixes

* add --cache_test_results=no to Bazel CI build ([c02cffc](https://www.github.com/googleapis/gapic-generator-php/commit/c02cffc6584c114127ff7e634c8db0471af225d8))
* add bazel clean to CI build ([682a556](https://www.github.com/googleapis/gapic-generator-php/commit/682a5564ca0c8cac67c5e0093e862f27738e6c25))
* add clarifying comments to CI config file ([380c98f](https://www.github.com/googleapis/gapic-generator-php/commit/380c98f8c1d56d412bb44f749b21155b69037847))
* clean php Bazel artifacts ([feba4d3](https://www.github.com/googleapis/gapic-generator-php/commit/feba4d303218f3ec3128be5c711d19e2dd527b1c))
* clean php Bazel artifacts, take 2 ([f0ffaa9](https://www.github.com/googleapis/gapic-generator-php/commit/f0ffaa954a9c80da3b61de67fbbffdf5e22c8724))
* **phpdoc:** Generate [@experimental](https://www.github.com/experimental) tags only for alpha/beta APIs ([99ccdea](https://www.github.com/googleapis/gapic-generator-php/commit/99ccdea05a55ed64a1dc8c7615bec1789153ee1f))
* remove Bazel cache linking steps ([c57dd26](https://www.github.com/googleapis/gapic-generator-php/commit/c57dd260a655d1e2fcdca0a739919b4a1d851ff8))
* remove debug printfs ([33cac32](https://www.github.com/googleapis/gapic-generator-php/commit/33cac32a8ca992e35721d99473334be7610363ef))
* **resnames:** Fix repeated resname default value generation in tests ([ff9fdcb](https://www.github.com/googleapis/gapic-generator-php/commit/ff9fdcb02360664ad33ee0d361ef2b861f03dfa2))
* **resnames:** Handle message-field resources in path template construction ([d95e29d](https://www.github.com/googleapis/gapic-generator-php/commit/d95e29d8ea6752b8dc8e9fe9ff7906707f833648))
* **resnames:** Use formattedName helpers in tests and samples ([8b38ce8](https://www.github.com/googleapis/gapic-generator-php/commit/8b38ce80a21c0b018f581afab8c06ba197edbad4))
* **resnames:** Use resource parts for path template prop names ([4d417b7](https://www.github.com/googleapis/gapic-generator-php/commit/4d417b731de864a7e2ad0d4cce89e8fd6f3d092f))
* test GitHub actions ([547fbb8](https://www.github.com/googleapis/gapic-generator-php/commit/547fbb80a8fe27e7ffec2fab6d2fad2ab3983d18))
* test GitHub actions ([019ffe5](https://www.github.com/googleapis/gapic-generator-php/commit/019ffe5ca705c5279ec1e25c67d97cb098558c60))
* test GitHub actions ([c101ddd](https://www.github.com/googleapis/gapic-generator-php/commit/c101ddd36a7b2ceb213f953ba183342fdee161e4))
* test GitHub actions ([a51448d](https://www.github.com/googleapis/gapic-generator-php/commit/a51448d8810da775604b62475ee89f896c2a5390))
* test GitHub actions ([548656a](https://www.github.com/googleapis/gapic-generator-php/commit/548656a3e8233a484660ac48b04ac34c6c7f0ae8))
* test GitHub actions ([5b82377](https://www.github.com/googleapis/gapic-generator-php/commit/5b82377a42b61f553bb55a19eb76752d01d86d85))
* test GitHub actions ([0109946](https://www.github.com/googleapis/gapic-generator-php/commit/0109946b07eb585fc1184dcd5b23a73a15874333))
* **tests:** Update unit test goldens (fix merge, borked Bazel CI) ([24389ec](https://www.github.com/googleapis/gapic-generator-php/commit/24389eca29a9d6b8b7c3380edf555a3901825df2))
* undo Bazel caching cleaning ([0d57b1a](https://www.github.com/googleapis/gapic-generator-php/commit/0d57b1a33a453a00668bbd368570437bec342925))
* Update goldens ([e3c52b1](https://www.github.com/googleapis/gapic-generator-php/commit/e3c52b1bb3cde59ad394cd9d0dcd72e965780218))
* update securitycenter goldens ([89cdcb6](https://www.github.com/googleapis/gapic-generator-php/commit/89cdcb63b6c554a934d9780f9a3f0d2dfbd80e5a))
* update unit goldens ([a66fcf8](https://www.github.com/googleapis/gapic-generator-php/commit/a66fcf8838a2d3e189177b55e9dea2e641768197))
* update unit goldens ([feb1435](https://www.github.com/googleapis/gapic-generator-php/commit/feb1435279f677faae534019d26ba114da5e2aee))

### [0.0.6](https://www.github.com/googleapis/gapic-generator-php/compare/v0.0.5...v0.0.6) (2021-03-24)


### Bug Fixes

* **client:** Generate requestParams descriptor for RPCs with >1 routing headers ([0f31c80](https://www.github.com/googleapis/gapic-generator-php/commit/0f31c8022c0963ed898268854542b81df93a005c))
* **client:** handle >1 routing headers ([9537d24](https://www.github.com/googleapis/gapic-generator-php/commit/9537d24b428062f0a2801e5db9a4532c38137589))
* **client:** Handle optional and required request params (phase 2/2) ([b9d6e85](https://www.github.com/googleapis/gapic-generator-php/commit/b9d6e85367f907f9ee066e5b912bc73eb883be31))
* **client:** set request parameters for only required fields (phase 1/2) ([2c7a6f4](https://www.github.com/googleapis/gapic-generator-php/commit/2c7a6f43f2db5fb5dfd1fe726575ac5ad1540149))
* **lint:** relint all files ([3fbeb7b](https://www.github.com/googleapis/gapic-generator-php/commit/3fbeb7bf416b5466d900b6a6b1b24ae638a2d6f2))
* **lint:** use multi-line docblocks in generated srcs ([45dfd30](https://www.github.com/googleapis/gapic-generator-php/commit/45dfd30b85846c1f26f440185a2795362099e915))
* **tests:** Add initial dataproc goldens ([79ea42e](https://www.github.com/googleapis/gapic-generator-php/commit/79ea42e5eb6490ca7184e63c2bb906531fcbf089))
* **tests:** Add initial functions goldens ([6ce39d6](https://www.github.com/googleapis/gapic-generator-php/commit/6ce39d6d91334bc96cf7de179ed7000ec1f220b3))
* unit test golden update ([ac9c066](https://www.github.com/googleapis/gapic-generator-php/commit/ac9c066e48858395361f224b780e9abd6986257b))
