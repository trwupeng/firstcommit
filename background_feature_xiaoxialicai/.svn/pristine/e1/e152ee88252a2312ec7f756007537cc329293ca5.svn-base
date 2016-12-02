fix include in all ice file by using __DIR__;
then in client file use
  require '/var/www/SoohIce/ice-phplib/Ice.php';

otherwize use
  ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . '/root/ice-3.6.1/php/lib');
  require 'Ice.php';

fix class_exists,interface_exists, add false, otherwize, error will be triggerd in autoload
