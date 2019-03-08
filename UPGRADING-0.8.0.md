# Deprecations

The next major release of NObjects will incorporate API changes that will make the library PHP7
compatible. In order to assist with migration to the new versions, silent deprecation errors will be
triggered by all class constructors and public static methods which will undergo breaking API
changes in the next major release.

```
@trigger_error('Deprecation notice', E_USER_DEPRECATED);
```

In order to use these errors to help in migration, you should add a custom error handler to log them,
using set_error_handler. If you are using Symfony, you may use the PHPUnit Bridge project in order to
capture these notices. See https://github.com/symfony/phpunit-bridge.

## Affected Classes

### NObjects\Object

"Object" is a reserved word in PHP 7. Class NObjects\Object will be renamed in a future version of
the library.

Migration step: Use the class NObjects\Nobject instead of NObjects\Object.

### NObjects\String

NObjects\String must be renamed as "String" is a reserved word in PHP 7. Use NObjects\StringUtil instead.
 
Migration step: Invoke methods of class NObjects\StringUtil instead of NObjects\String.

### NObjects\Cipher

The Mcrypt library interface is deprecated as of PHP 7.1. The encryption algorithms supported by 
"levels" 1, 2, and 3 do not have equivalents in the OpenSSL library or in currently available
polyfill libraries, so these algorithms will no longer be able to be supported.

NObjects encryption level 4 maps to the Mcrypt implementation of Rijndael 256. This algorithm slightly
differs from AES-256 because of its variable key size. AES-256 is offered in openssl, but not Rinjndael 256.

Both Cipher::encrypt and Cipher::decrypt methods are deprecated. You are recommended to migrate your 
encryption and hashing needs to a different library. 

For example, this is a popular and well-maintained encryption and hashing library.

  | defuse/php-encryption | https://github.com/defuse/php-encryption | https://packagist.org/packages/defuse/php-encryption |

In order to preserve the functionality unrelated to mcrypt present in NObjects\Cipher, the 
NObjects\Cipher::md5Data method will be migrated to NObjects\Hash::md5.

Migration steps:
  1. Create a migration plan for stored encrypted data and strings that were encrypted with the above algorithms.
  2. Migrate calls to NObjects\Cipher::encrypt() and NObjects\Cipher::decrypt() to your chosen alternative.
  2. Change invocations of NObjects\Cipher::md5Data() to NObjects\Hash::md5() with the same method signature.
  
### NObjects\Cache\Apc

APC has been replaced by APCU in PHP 7. NObjects\Cache\Apc will select the functions for the loaded extension, and
will prefer apcu if it is loaded.

Migration steps: None

### NObjects\Cache\Memcache

The implementation of NObjects\Cache\Memcache tightly parallels the \Memcache class, which has been
abandoned.

Since the creation of NObjects, community standards [PSR-6](https://www.php-fig.org/psr/psr-6/)
and [PSR-16](https://www.php-fig.org/psr/psr-16/), as well as reference implementations of those
above, have emerged. It's recommended to migrate your code to use a package that implements one
of these community standards which suits your situation.

Migration steps:
1. Choose a suitable PSR-6 or PSR-16 implementation.
2. Locate usages of NObjects\Cache\Memcache in your architecture, and migrate to your preferred package.

