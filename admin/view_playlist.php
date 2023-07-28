<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:playlist.php');
}

if(isset($_POST['delete_playlist'])){
   $delete_id = $_POST['playlist_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   $delete_playlist_thumb = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
   $delete_playlist_thumb->execute([$delete_id]);
   $fetch_thumb = $delete_playlist_thumb->fetch(PDO::FETCH_ASSOC);
   unlink('../uploaded_files/'.$fetch_thumb['thumb']);
   $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE playlist_id = ?");
   $delete_bookmark->execute([$delete_id]);
   $delete_playlist = $conn->prepare("DELETE FROM `playlist` WHERE id = ?");
   $delete_playlist->execute([$delete_id]);
   header('locatin:playlists.php');
 }else{
     $message[] = 'playlist was already deleted';
  }


   




?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Playlist Details</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-details">

   <h1 class="heading">playlist details</h1>

   <?php
      $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE id = ? LIMIT 1");
      $select_playlist->execute([$get_id]);
      if($select_playlist->rowCount() > 0){
         while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
            $playlist_id = $fetch_playlist['id'];
            $count_content = $conn->prepare("SELECT * FROM 'content' WHERE playlist_id = ?");
            $count_content->execute([$get_id]);
            $total_content = $count_content->rowCount();
   ?>
   <div class="row">
      <div class="thumb">
         <img src="../uploaded_files/<?= $fetch_playlist['thumb']; ?>" alt="">
      </div>
      <div class="flex">
        <p><i class="fas fa-videos"></i> <span><?= $total_contents; ?></span></p> 
        <p><i class="fas fa-calendar"></i><span><?= $fetch_playlist['date']; ?></span></p>
      </div>
      <div class="details">
         <h3 class="title"><?= $fetch_playlist['title']; ?></h3>
         <p class="description"><?= $fetch_playlist['description']; ?></p>
         <form action="" method="POST" class="flex-btn">
            <input type="hidden" name="playlist_id" value="<?= $fetch_playlist_id; ?>">
            <a href="update_playlist.php?get_id=<?= $fetch_playlist_id; ?>" class="option-btn">update</a>
            <input type="submit" value="delete playlist" class="delete-btn"  name="delete">
         </form>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no playlist found!</p>';
      }
   ?>

</section>

<section class="contents">

   <h1 class="heading">playlist videos</h1>

   <div class="box-container">

   <?php
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ? AND playlist_id = ?");
      $select_content->execute([$tutor_id, $playlist_id]);
      if($select_videos->rowCount() > 0){
         while($fecth_content = $select_content->fetch(PDO::FETCH_ASSOC)){ 
            $video_id = $fecth_videos['id'];
   ?>
      <div class="box">
         <div class="flex">
            <div><i class="fas fa-dot-circle" style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"></i><span style="<?php if($fecth_videos['status'] == 'active'){echo 'color:limegreen'; }else{echo 'color:red';} ?>"><?= $fecth_videos['status']; ?></span></div>
            <div><i class="fas fa-calendar"></i><span><?= $fecth_content['date']; ?></span></div>
         </div>
         <img src="../uploaded_files/<?= $fecth_content['thumb']; ?>"  alt="">
         <h3 class="title"><?= $fecth_content['title']; ?></h3>
         <a href="view_content.php?get_id=<?= $fecth_content['id']; ?>" class="btn">view content</a>

         <div class="flex-btn">
            <form action="" class="flex-btn" method="POST">
               <input type="hidden" name="content_id" value="<?= $fetch_content['id']; ?>">
               <a href="update_content.php?get_id=<?= $fetch_content['id']; ?>" class="option-btn">update</a>
               <input type="submit" value="delete" name="delete_content" class="delete-btn">
            </form>
         </div>
      </div>
   <?php
         }
      }else{
         echo '<p class="empty">no content added yet! <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add new content</a></p>';
      }
   ?>
   </div>

</section>

 
<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>