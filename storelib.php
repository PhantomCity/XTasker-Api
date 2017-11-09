<?PHP


function storage_open($FN)
{
  $hasDB = file_exists($FN);
  $db = new PDO('sqlite:' . $FN);
  if (!$hasDB )
  {
	  $db->exec('CREATE TABLE `Data` (`User` text, `RecordID` integer, `Deleted` integer,`FieldName` text, `Value`, `ParentField` integer);');
	  $db->exec('CREATE TABLE `UserFields` (`User` text, `FieldName` text, `Title` text, `ParentFieldId` integer, `DefaultValue`);');
  }
  
  $N = $db->lastInsertId();
  
  return $db;
}


/*
function storage_request($db, $user, $fields)
{
  $i = 0;
  $req = '';
  $ra = array();
  $ra[':user'] = $user;
  
  foreach ($fields as $k => $v)
  {
    $i++;
    $ra['Value'.($i)] = $v;
    $ra['Field'.($i)] = $k;
    $s = '((:Value'.($i).' == Value) and (:Field'.($i).' == FieldName))';
    if (1 == $i)
      $req = $s;
    else
      $req = $req . ' or ' . $s;
  }
  $stm = $db->prepare('Select * from `Data` where (0 == Deleted) and ( RecordID in (select RecordID from `Data` where (:user == User) and (0 == Deleted) and ( ' . $req . ' ) group by RecordID)  );');
  $stm->execute($ra);
  
  return $stm->fetchAll(PDO::FETCH_ASSOC);
}
*/

function storage_append($db, $user, $fields)
{  
  $stmt = $db->query("SELECT Count() from `Data`;");
  $N = $stmt->fetchColumn();
  $RI = md5((1 +$N) + ($user));
  
  try
  {  
    $db->beginTransaction();   
    $qry = $db->prepare('insert into `Data` (User, RecordID, Deleted, FieldName, Value, ParentField) values (:User, :RI, :DL, :FN, :V, :PR)');  
    
    foreach ($fields as $row)
    foreach ($row as $k=>$v)
    {
      $par = array(':User' => $user, ':RI' => $RI, ':DL' => 0, ':PR' => -1, ':FN' => $k, ':V'=> $v);
      $qry->execute($par);
    }
  }
  catch (Exception $e){}
  finally
  {
    $db->commit();   
  }
}

?>