<?php

namespace Kenjiefx\VentaCss\Cli;

class CoutStreamer {

  public static function cout(
    $content,
    $color=null
    )
  {

    $cheader = '';
    $cfooter = "\033[0m";

    // let check which color code was used so we can then wrap our content.
    switch ($color) {
      case 'error':
        $cheader .= "\033[31m";
        break;
      case 2:
      case 'success':
        $cheader .= "\033[32m";
        break;
      case 3:
      case 'yellow':
        $cheader .= "\033[33m";
        break;
      case 4:
      case 'blue':
        $cheader .= "\033[34m";
        break;
      case 5:
      case 'magenta':
        $cheader .= "\033[35m";
        break;
      case 6:
      case 'cyan':
        $cheader .= "\033[36m";
        break;
      case 7:
      case 'light grey':
        $cheader .= "\033[37m";
        break;
      case 8:
      case 'dark grey':
        $cheader .= "\033[90m";
        break;
      case 9:
      case 'light red':
        $cheader .= "\033[91m";
        break;
      case 10:
      case 'light green':
        $cheader .= "\033[92m";
        break;
      case 11:
      case 'light yellow':
        $cheader .= "\033[93m";
        break;
      case 12:
      case 'light blue':
        $cheader .= "\033[94m";
        break;
      case 13:
      case 'light magenta':
        $cheader .= "\033[95m";
        break;
      case 14:
      case 'light cyan':
        $cheader .= "\033[92m";
        break;
    }
    // wrap our content.
    $content = $cheader.$content.$cfooter;
    //return our new content.
    echo $content.PHP_EOL;
  }

}
