<?php declare(strict_types=1);

namespace {

final class MyGMP {
  private static ?\FFI $instance = null;

  /* @@mpz_t */
  private object $mpz;

  public function __construct(string $initval = '0') {
    $instance = self::_getInstance();
    $this->mpz = $instance->new('mpz_t');
    $instance->__gmpz_init($this->mpz);
    $instance->__gmpz_set_str($this->mpz, $initval, 0);
  }

  public function __destruct() {
    self::_getInstance()->__gmpz_clear($this->mpz);
  }

  private static function _getInstance(): \FFI {
    if (self::$instance) {
      return self::$instance;
    }

    $header = <<<HEADER
        typedef unsigned long long int mp_limb_t;
        typedef long long int mp_limb_signed_t;

        typedef struct {
          int _mp_alloc;
          int _mp_size;
          mp_limb_t *_mp_d;
        } __mpz_struct;
        typedef __mpz_struct mpz_t[1];
        typedef __mpz_struct *mpz_ptr;
        typedef const __mpz_struct *mpz_srcptr;

        void __gmpz_init(mpz_ptr);
        void __gmpz_clear(mpz_ptr);

        int __gmpz_set_str(mpz_ptr, const char *, int);
        HEADER;

    self::$instance = \FFI::cdef($header, 'libgmp.so');

    return self::$instance;
  }
}

}  // namespace
