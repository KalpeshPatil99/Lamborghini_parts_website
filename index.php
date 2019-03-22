<?php
session_start();
$product_ids = array();
//session_destroy();

//check if Add to Cart button has been clicked
if(filter_input(INPUT_POST, 'add_to_cart')){
    if(isset($_SESSION['shopping_cart'])){
        
        //how many products are there in shopping cart currently
        $count = count($_SESSION['shopping_cart']);
        
        $product_ids = array_column($_SESSION['shopping_cart'], 'id');
        
        if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
        $_SESSION['shopping_cart'][$count] = array
            (
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST, 'name'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity')
            );   
        }
        else { //if product already exists increase quantity by 1
            //match array key to id of the product being added to the cart
            for ($i = 0; $i < count($product_ids); $i++){
                if ($product_ids[$i] == filter_input(INPUT_GET, 'id')){
                    //add item quantity to the product
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                }
            }
        }
        
    }
    else { //if shopping cart doesn't exist, create first product with array key 0
        //create array using submitted form data, start from key 0 and fill it with values
        $_SESSION['shopping_cart'][0] = array
        (
            'id' => filter_input(INPUT_GET, 'id'),
            'name' => filter_input(INPUT_POST, 'name'),
            'price' => filter_input(INPUT_POST, 'price'),
            'quantity' => filter_input(INPUT_POST, 'quantity')
        );
    }
}

if(filter_input(INPUT_GET, 'action') == 'delete'){
    foreach($_SESSION['shopping_cart'] as $key => $product){
        if ($product['id'] == filter_input(INPUT_GET, 'id')){
            unset($_SESSION['shopping_cart'][$key]);
        }
    }
    $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}

function pre_r($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
?>
<!DOCTYPE html>
<html>
	<head>
	<title>Lamborghini</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link href="css/styles.css" type="text/css" rel="stylesheet">
	<link href="css/bootstrap.css" type="text/css" rel="stylesheet">
	<link href="css/cart.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" href="css/font-awesome.css">
	<link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
	</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top">
	  <div class="container-fluid">
	    <!-- Brand and toggle get grouped for better mobile display -->
	    <div class="navbar-header">
	      <button type="button" class="navbar-toggle collapse" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      <a class="navbar-brand" href="#header" id="scrollTop"><img src="img/text.png"></a>
	    </div>

	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul class="nav navbar-nav navbar-right">
	        <li class="active"><a href="#header">Home <span class="sr-only">(current)</span></a></li>
	      </ul>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>

	<!--Landing-->
	<section class="heading text-center" id="header">
		<div class="intro">	
			<div class="inner">	
	        	<h1>Cars built just for you</h1>
	           	<a href="#content" class="link">Let's Shop</a>
	    	</div>
	    </div>
	</section>

	<!--Content-->
	<section class="content text-center" id="content">
		<div class="container"> 
			<h1>&bullet;&nbsp;Lamborghini Parts&nbsp;&bullet;</h1>
			<?php

		    $connect = mysqli_connect('localhost', 'root', '', 'accounts');
		    $query = 'SELECT * FROM products ORDER by id ASC';
		    $result = mysqli_query($connect, $query);

		    if ($result):
		        if(mysqli_num_rows($result)>0):
		            while($product = mysqli_fetch_assoc($result)):
		            ?>
					<div class="card col-sm-4 col-md-3">
						<form method="post" action="index.php?action=add&id=<?php echo $product['id']; ?>#table">	
							<div class="img_sq">
								<img src="<?php echo $product['image']; ?>"/>
							</div>
							<h3 class="obj"><?php echo $product['name']; ?></h3>
							<h3 class="obj">$ <?php echo $product['price']; ?></h3>
							<div class="desc">
								<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
							</div>
							<input type="text" name="quantity" class="form-control" placeholder="Enter Quantity" style="background: #4c4c4c; color: #fff; border:1px solid #ffa500; font-family: 'Poppins',sans serif;" value="1" />
                            <input type="hidden" name="name" value="<?php echo $product['name']; ?>" />
                            <input type="hidden" name="price" value="<?php echo $product['price']; ?>" />
                            <input type="submit" name="add_to_cart" style="margin-top:5px;" class="link"
                                   value="Add to Cart" />
						</form>
					</div>
					<?php
                endwhile;
            endif;
        endif;   
        ?>
		<div style="clear:both"></div>  
        <br />  
        <div class="table-responsive" id="table">  
        <table class="table" style="font-family: 'Poppins',sans serif;">  
            <tr><th colspan="5"><h3>Order Details</h3></th></tr>   
        <tr>  
             <th width="40%">Name</th>  
             <th width="10%">Quantity</th>  
             <th width="20%">Price</th>  
             <th width="15%">Total</th>  
             <th width="5%">Action</th>  
        </tr>  
        <?php   
        if(!empty($_SESSION['shopping_cart'])):  
            
             $total = 0;  
        
             foreach($_SESSION['shopping_cart'] as $key => $product): 
        ?>  
        <tr>  
           <th><?php echo $product['name']; ?></th>  
           <th><?php echo $product['quantity']; ?></th>  
           <th>$ <?php echo $product['price']; ?></th>  
           <th>$ <?php echo number_format($product['quantity'] * $product['price'], 2); ?></th>  
           <th>
               <a href="index.php?action=delete&id=<?php echo $product['id']; ?>#table">
                    <div class="btn-danger">Remove</div>
               </a>
           </th>  
        </tr>  
        <?php  
                  $total = $total + ($product['quantity'] * $product['price']);  
             endforeach;  
        ?>  
        <tr>  
             <th colspan="3" align="right">Total</th>  
             <th>$ <?php echo number_format($total, 2); ?></th>  
             <th></th>  
        </tr>  
        <tr>
            <!-- Show checkout button only if the shopping cart is not empty -->
            <td colspan="5">
             <?php 
                if (isset($_SESSION['shopping_cart'])):
                if (count($_SESSION['shopping_cart']) > 0):
             ?>
                <a class="button" name="checkout">Checkout</a>
                <?php
                	$newName = $product['name'];
                	$newQuan = $product['quantity'];
					$db = mysqli_connect('localhost', 'root', '', 'carts');
					
					$query = "INSERT INTO orders (name, quantity, price) 
					          VALUES('$newName','$newQuan','$total')";
					mysqli_query($db, $query);
				?>
             <?php endif; endif; ?>
            </th>
        </tr>
        <?php  
        endif;
        ?>  
        </table>  
        </div>
		</div>
	</section>

	<!--Footer Start-->
	<footer id="footer" class="text-center"> 
		<div class="container">
		    <div class="icons">
		    	<h3>&bullet;&nbsp;Follow Me&nbsp;&bullet;</h3>
		    	<a href="https://www.facebook.com/kalpesh.patil.50364" target="_blank"><i class="fa fa-facebook"></i></a>
		    	<a href="https://www.instagram.com/_kalpesh.patil_" target="_blank"><i class="fa fa-instagram"></i></a>
		    </div>
		    <hr class="style">
		    <div class="final">
		    	<h5 title="Hello World! Have a Nice Day" data-tooltip>Created with&nbsp;<i class="fa fa-heart"></i>&nbsp;by Kalpesh Patil</h5>
		    </div>
		</div> <!-- end container --> 
	</footer> <!-- end footer -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>
	<script>
		$(function() {
			$('[data-tooltip]').tooltip();
		});
	</script>
</body>
</html>