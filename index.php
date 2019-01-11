<?php
//start session
session_start();
//***************MySQLi Procedural*************
//connect to database
$servername = 'localhost';
$username = 'root';
$password = '';
$datebase = 'db1';
$conn = mysqli_connect($servername, $username, $password, $datebase);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// echo "Connected successfully";

//if "add to cart" button clicked
if(isset($_POST['add_to_cart'])){
  //if SESSION has data (cart not empty)
  if(isset($_SESSION['shopping_cart'])){
      //get all item ID from cart
     $item_array_id = array_column($_SESSION['shopping_cart'], 'item_id');
     //if item ID not in cart
     if(!in_array($_GET['id'], $item_array_id)){
       //get item number
       $count = count($_SESSION['shopping_cart']);
       //get new item info
       $item_array = array(
         'item_id'     => $_GET['id'],
         'item_name'   => $_POST['hidden_name'],
         'item_price'   => $_POST['hidden_price'],
         'item_quantity'   => $_POST['quantity']
       );
       //save new item info to session
       $_SESSION['shopping_cart'][$count] = $item_array;
     }else{ //if item already in cart, give alert and redirect to index
       echo '<script>alert ("Item Already Added")</script>';
       echo '<script>window.location="index.php"</script>';
     }
  }else{ //if cart has no item
    //get new item info
    $item_array = array(
      'item_id'     => $_GET['id'],
      'item_name'   => $_POST['hidden_name'],
      'item_price'   => $_POST['hidden_price'],
      'item_quantity'   => $_POST['quantity']
    );
    //save item info to session
    $_SESSION['shopping_cart'][0] = $item_array;
  }
}
//if action is set
if(isset($_GET['action'])){
  //if action's value = delete
  if($_GET['action'] == 'delete'){
    //loop through Values in Session
    foreach($_SESSION['shopping_cart'] as $key => $values){
      //if item (in session) item it = GET[id]
      if($values['item_id'] == $_GET['id']){
        //delete current session value
        unset($_SESSION['shopping_cart'][$key]);
        echo '<script>alert("Item Removed")</script>';
        echo '<script>window.location="index.php"</script>';
      }
    }
  }
}
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="style.css">
  <title>Document</title>
</head>
<body>
  <div>
    <h3 align="middle" style="width:700px">Shopping Cart</h3>
    <?php
      $query = 'select * from products order by id asc';
      //get all items info from database
      $result = mysqli_query($conn, $query);
      //if result not empty
      if(mysqli_num_rows($result) > 0){
        //loop through result
        while ($row = mysqli_fetch_array($result)) {
      ?>
        <div style="border:1px solid #333; background-color:#f1f1f1; display: inline-block" >
          <form action="index.php?action=add&id=<?php echo $row['id']?>" method="post">
          <center><img src="<?php echo $row['image']; ?>" alt="" /></center>
          <h4><?php echo $row['name'];?></h4>
          <h4><?php echo $row['price'];?></h4>
          <input type="text" name="quantity" value="1">
          <input type="hidden" name="hidden_name" value="<?php echo $row['name'];?>">
          <input type="hidden" name="hidden_price" value="<?php echo $row['price'];?>">
          <input type="submit" name="add_to_cart" value="Add to Cart">
        </form>
        </div>
      <?php
        }
      }
     ?>
  </div>
<h3>Order details</h3>
<div class="">
  <table border="1" style="border-collapse:collapse">
    <tr>
      <th width="20%">Item Name</th>
      <th width="10%">Quantity</th>
      <th width="20%">Price</th>
      <th width="15%">Total</th>
      <th width="5%">Action</th>
    </tr>
    <?php
      //if session not empty, display
      if(!empty($_SESSION['shopping_cart'])){
        $total = 0;  // Totoal price
        foreach($_SESSION['shopping_cart'] as $keys  => $values){
        ?>
        <tr>
          <td><?php echo $values['item_name']; ?></td>
          <td><?php echo $values['item_quantity']; ?></td>
          <td><?php echo $values['item_price']; ?></td>
          <td><?php echo number_format($values['item_quantity'] * $values['item_price'], 2); ?></td>
          <td><a href = "index.php?action=delete&id=<?php echo $values['item_id'] ?>" <span>Remove</span></a></td>
        </tr>
        <?php
          $total = $total + ($values['item_quantity'] * $values['item_price']);
        }
        ?>
          <tr>
            <td colspan="3" align="right">Total</td>
            <td align="right">$<?php echo number_format($total, 2); ?></td>
            <td></td>
          </tr>
        <?php
      }
     ?>
  </table>
</div>
</body>
</html>
