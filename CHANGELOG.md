# Changelog

## [0.2.0](https://www.github.com/googleapis/gapic-generator-php/compare/v0.1.2...v0.2.0) (2021-05-06)


### Features

* support REGAPIC pagination, add compute_small goldens [ggp] ([#278](https://www.github.com/googleapis/gapic-generator-php/issues/278)) ([81efb9f](https://www.github.com/googleapis/gapic-generator-php/commit/81efb9f7b71047d0ec097b604226c829f3e0ea99))


### Bug Fixes

* **diregapic:** pipe transport flag through Bazel, add Bazel REGAPIC integration test [ggp] ([#276](https://www.github.com/googleapis/gapic-generator-php/issues/276)) ([832a42f](https://www.github.com/googleapis/gapic-generator-php/commit/832a42f5e9ef454852005d743c5b40fc30b0fd61))

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
