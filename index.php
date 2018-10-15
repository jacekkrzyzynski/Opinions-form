<?php

$db = new PDO("sqlite:opinions.sqlite");

$db->exec('CREATE TABLE IF NOT EXISTS opinions (
  time INTEGER,
  firstname VARCHAR(100),
  lastname VARCHAR(100),
  email VARCHAR(100),
  ip VARCHAR(15),
  topic VARCHAR(100),
  image VARCHAR(100),
  rating TINYINT(1),
  opinion text
  )');


if (isset($_POST['formsubmitted'])) 
{
  if ($_FILES['image']['name'] && $_FILES["image"]["error"] == 0)
  {
    
    $uploadedFilename = basename($_FILES['image']['name']);
    $uploadedFilename = preg_replace('/[^\da-z\.]/i', '', $uploadedFilename);
    
    $timestamp = time();
    $imagesFoder = "avatars/";
      
    $fullsizeFilename = $timestamp."_".$uploadedFilename;
    $thumbsizeFilename = "thumb_".$timestamp."_".$uploadedFilename;
    
    move_uploaded_file($_FILES['image']['tmp_name'], $imagesFoder.$fullsizeFilename);
    
    $filenameParts = explode('.', $fullsizeFilename);
    $filenameExtension = strtolower(array_pop($filenameParts));
    
    if ($filenameExtension != 'jpg' && $filenameExtension != 'jpeg' && $filenameExtension != 'png')
    {
      echo "Bad input data!";
      die();
    }
  
    if ($filenameExtension == 'jpg' || $filenameExtension == 'jpeg')
      $sourceImage = imagecreatefromjpeg($imagesFoder.$fullsizeFilename);
    elseif ($filenameExtension == 'png')
      $sourceImage = imagecreatefrompng($imagesFoder.$fullsizeFilename);
  
    $imageSize = getimagesize($imagesFoder.$fullsizeFilename);
    $imageWidth = $imageSize[0];
    $imageHeight = $imageSize[1];
    
    if ($imageWidth > $imageHeight)
    {
      $y = 0;
      $x = ($imageWidth - $imageHeight) / 2;
      $shortSide = $imageHeight;
    }
    else
    {
      $x = 0;
      $y = ($imageHeight - $imageWidth) / 2;
      $shortSide = $imageWidth;
    }
  
    $thumb = imagecreatetruecolor(150, 150);
    imagecopyresampled($thumb, $sourceImage, 0, 0, $x, $y, 150, 150, $shortSide, $shortSide);
    
    $watermarkImage = imagecreatefrompng('watermark.png');
    imagecopy($thumb, $watermarkImage, 0, 0, 0, 0, 150, 150);
      
    if ($filenameExtension == 'jpg' || $filenameExtension == 'jpeg')
      imagejpeg($thumb, $imagesFoder.$thumbsizeFilename);
    elseif ($filenameExtension == 'png')
      imagepng($thumb, $imagesFoder.$thumbsizeFilename);
  }
  else $fullsizeFilename = "";
  
  foreach($_POST as $k=>$v) {
    $v = strip_tags($v);
    $$k = trim($v);
    }

  if (!$firstname || !$lastname || !$email || !$rating || !filter_var($email, FILTER_VALIDATE_EMAIL))
  {
    echo "Bad input data!";
    die();
  }
      
  $ip = $_SERVER['REMOTE_ADDR'];
  if ($ip == "::1") $ip = "localhost";
    
  $q = "INSERT INTO opinions (time, firstname, lastname, email, ip, topic, image, rating, opinion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";    
  $sth = $db->prepare($q);
  $sth->execute(array(time(), $firstname, $lastname, $email, $ip, $topic, $fullsizeFilename, $rating, $opinion));

  header('Location: /');
  die();

}
?><!DOCTYPE html>
<html lang="pl-PL">
<head>
  <title>Jacek Krzyżyński - moduł opinii dla BRAINBOX</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <script src="opinions.js?ver=20181014a"></script>
  <style>
  body {
    padding-top: 30px;
    }
  @media (max-width: 980px) {
    body {
      padding-top: 0;
    }
  }
  </style>  
</head>
<body>
<div class="container">   
<?php

if (isset($_GET['dodaj_opinie']))
{
  ?>
  
  <a href="/" class="btn btn-info" role="button">Anuluj</a>
  <h2>Dodaj opinię</h2>
  <div class="alert alert-danger" id="erroralert" style="display:none;">
    <strong>Błąd!</strong> Uzupełnij dane formularza! <strong>(<span id="erroralertmessage"></span>)</strong>
  </div>
  <form method="post" id="form" action="/" enctype="multipart/form-data">
  <input type="hidden" name="formsubmitted" value="1">
  <div class="form-group">
    <label for="firstname">Imię:</label> <small><em>pole wymagane</em></small>
    <input class="form-control" id="firstname" name="firstname">
  </div>
  <div class="form-group">
    <label for="lastname">Nazwisko:</label> <small><em>pole wymagane</em></small>
    <input class="form-control" id="lastname" name="lastname">
  </div>
  <div class="form-group">
    <label for="email">Adres email:</label> <small><em>pole wymagane</em></small>
    <input class="form-control" id="email" name="email">
  </div>
  <div class="form-group">
    <label for="topic">Temat:</label>
    <select class="form-control" id="topic" name="topic">
      <option>Ocena produktu</option>
      <option>Ocena firmy</option>
      <option>Inne</option>
    </select>
  </div>   
  <div class="form-group">
    <label for="image">Avatar (<small><em>max 2MB</em></small>):</label>
    <input type="file" class="form-control-file" accept="image/jpg,image/png"  id="image" name="image">
  </div>
  <div class="form-group">
    <label for="rating">Ocena:</label>
    <div>
    <input type="hidden" name="rating" id="rating" value="1">
    <span class="glyphicon glyphicon-star" id="rate_1" style="color:#f00;"></span>
    <span class="glyphicon glyphicon-star" id="rate_2"></span>
    <span class="glyphicon glyphicon-star" id="rate_3"></span>
    <span class="glyphicon glyphicon-star" id="rate_4"></span>
    <span class="glyphicon glyphicon-star" id="rate_5"></span>
    </div>
  </div>  
  <div class="form-group">
    <label for="opinion">Treść:</label>
    <textarea class="form-control" id="opinion" name="opinion"></textarea>
  </div>  
  <button type="submit" class="btn btn-success">Submit</button>
  </form> 
  <?php
}

else
{
  ?>
  
  <a href="/dodaj_opinie" class="btn btn-info" role="button">Dodaj opinię</a>
  <h2>Opinie</h2>
  <?php
  
    $q = "SELECT * FROM opinions";    
    $sth = $db->prepare($q);
    $sth->execute(array());
    $results = $sth->fetchAll();
    
    $resultsCount = count($results);
    
    if ($resultsCount > 5) {
      $page = isset($_GET['page']) ? $_GET['page'] : 1;
      
      $page = intval($page);
      if ($page == 0) $page = 1;
      
      $offset = ($page - 1) * 5;
      
      $q = "SELECT * FROM opinions LIMIT 5 OFFSET $offset";    

      $sth = $db->prepare($q);
      $sth->execute(array());
      $results = $sth->fetchAll();
      
      $pagesNavNr = ceil($resultsCount / 5);
      
      echo '<nav aria-label="Pages">
    <ul class="pagination">
';
      
      for($i = 1; $i <= $pagesNavNr; $i++)
      {
        $prevPageNav = $page - 1;
        $nextPageNav = $page + 1;
        
        if ($i == 1) 
          $link = '/';
        else
          $link = '/strona/'.$i;
                    
        if ($page == $i)
          echo '      <li class="page-item active"><span class="page-link">'.$i.' <span class="sr-only">(current)</span></span></li>
';
        else
          echo '      <li class="page-item"><a class="page-link" href="'.$link.'">'.$i.'</a></li>
';
      }      
      
      echo '    </ul>
  </nav>
';
      }

    foreach($results as $k=>$result)
    {
      
      if ($result['image'])
        $imageCode = '<a href="/avatars/'.$result['image'].'" target="_blank"><img src="/avatars/thumb_'.$result['image'].'"></a>';
      else 
        $imageCode = '<img src="/watermark.png">';
      
      if ($result['opinion'] == "") $result['opinion'] = "Brak opinii";
      
      if ($result['ip'] != "localhost")
      {
        $ip = '<a href="https://geoiptool.com/en/?ip='.$result['ip'].'" target="_blank">'.$result['ip'].'</a>';
      }
      else
        $ip = $result['ip'];
      switch ($result['rating'])
      {
        case 1:
          $ratingStars = '
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star"></span>
          <span class="glyphicon glyphicon-star"></span>
          <span class="glyphicon glyphicon-star"></span>
          <span class="glyphicon glyphicon-star"></span>';
           break; 
        case 2:
          $ratingStars = '
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star"></span>
          <span class="glyphicon glyphicon-star"></span>
          <span class="glyphicon glyphicon-star"></span>';
           break; 
        case 3:
          $ratingStars = '
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star"></span>
          <span class="glyphicon glyphicon-star"></span>';
           break; 
        case 4:
          $ratingStars = '
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star"></span>';
           break; 
        case 5:
          $ratingStars = '
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>
          <span class="glyphicon glyphicon-star" style="color:#f00;"></span>';
           break;                                             
        }
        
      echo '<table class="table table-bordered">
  <tr>
    <td class="col-md-1">
      '.$imageCode.'    
    </td>
    <td>
      <p>Dodano: <span class="font-weight-light">'.date("d-m-Y H:i:s", $result['time']).'</span> przez: <strong><a href="mailto:'.$result['email'].'">'.$result['firstname'].' '.$result['lastname'].'</a></strong>
      (<em>'.$ip.'</em>)</p>
      <p>Ocena: '.$ratingStars.'</p>      
      <h2>'.$result['topic'].'</h2>
      <p class="font-weight-light">'.$result['opinion'].'</p>
    </td>
  </tr>
</table>
';

      
    }    
}
  


?>
</div>
</body>
</html>