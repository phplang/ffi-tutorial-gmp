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

  public function getString(int $base = 10): string {
    $instance = self::_getInstance();
    $len = $instance->__gmpz_sizeinbase($this->mpz, $base) + 2;
    $ret = $instance->new("char[$len]");
    $instance->__gmpz_get_str($ret, $base, $this->mpz);

    $str = '';
    for ($i = 0; $i < $len; ++$i) {
      if (ord($ret[$i]) == 0) break;
      $str .= $ret[$i];
    }

    return $str;
  }

  public function __toString() {
    return $this->getString(10);
  }

  public function __debugInfo() {
    return [
       2 => $this->getString(2),
       8 => $this->getString(8),
      10 => $this->getString(10),
      16 => $this->getString(16),
    ];
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

        size_t __gmpz_sizeinbase(mpz_srcptr, int);
        char *__gmpz_get_str(char *, int, mpz_srcptr);
        int __gmpz_set_str(mpz_ptr, const char *, int);
        HEADER;

    self::$instance = \FFI::cdef($header, 'libgmp.so');

    return self::$instance;
  }
}

}  // namespace
