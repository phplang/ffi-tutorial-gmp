<?php declare(strict_types=1);

namespace {

final class MyGMP {
  private static ?\FFI $instance = null;

  public static function _getInstance(): \FFI {
    if (self::$instance) {
      return self::$instance;
    }

    $header = <<<HEADER
        HEADER;

    self::$instance = \FFI::cdef($header, 'libgmp.so');

    return self::$instance;
  }
}

}  // namespace
