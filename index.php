<?PHP

include_once('storelib.php');

$USER_ID = '9254359d88ed69cd760b3bc672ae5548';
$DB_FILE = '../storage/bases/xnotes.sqlitedb';

unset($data);

$act = $_GET['a'];
if ( isset($_GET['jd']) ) //json data
{
  $data = json_decode($_GET['jd'], true);
  
  if (!(is_numeric(array_keys($data)[0]) ))
  {
    $r = [];
    $r[] = $data;
    $data = $r;
  }
}

/////////////////////////////////////////////////////////////

$db = storage_open($DB_FILE);

if ('add' == $act)
{
  storage_append($db, $USER_ID, $data);
  echo 'appended';
}




function SQL2Objs($fetch)
{
  $ret = [];
  foreach ($fetch as $row)
    $ret[ $row['RecordID'] ][$row['FieldName']] = $row['Value'];
  
  return $ret;
}

?>
