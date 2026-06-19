<?php 
include('includes/header.php'); 
include('../db_connection.php'); // adjust this path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $image_path = '';

    // Handle file upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $target_dir = "../img/";
        $filename = basename($_FILES['product_image']['name']);
        $target_file = $target_dir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            $image_path = str_replace('../', '', $target_file); // store relative path
        } else {
            echo "<div class='alert alert-danger text-center'>Image upload failed.</div>";
        }
    }

    // Insert into DB
    if ($image_path) {
        $stmt = $conn->prepare("INSERT INTO Menu_Items (Item_Name, Price, Image_Path) VALUES (?, ?, ?)");
        $stmt->bind_param("sds", $item_name, $price, $image_path);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center'>Product added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Failed to add product.</div>";
        }
    }
}
?>

<!-- Form -->
<div class="row">
  <div class="ms-3">
    <h3 class="mb-0 h4 font-weight-bolder">Add Product</h3>
    <p class="mb-4">Display your new products!</p>
  </div>
</div>

<div class="d-flex justify-content-center">
  <div class="col-xl-4 col-lg-5 col-md-7 border border-dark rounded p-3">
    <div class="card card-plain">

      <div class="card-header text-center">
        <h4 class="font-weight-bolder">Add New Product</h4>
        <p class="mb-0">Enter the details of your new product</p>
      </div>
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <!-- Product name -->
<div class="form-floating mb-3">
  <input type="text" class="form-control bg-white" id="item_name"
         name="item_name" placeholder=" " required>
  <label for="item_name">Product name</label>
</div>

<!-- Price -->
<div class="form-floating mb-3">
  <input type="number" step="0.01" class="form-control bg-white" id="price"
         name="price" placeholder=" " required>
  <label for="price">Price (₱)</label>
</div>

<!-- Image Upload -->
<div class="mb-3">
  <label for="product_image" class="form-label">Choose item image</label>
  <input type="file" class="form-control bg-white" id="product_image"
         name="product_image" accept="image/*" required>
</div>


          <div class="text-center">
            <button type="submit" class="btn btn-lg bg-gradient-dark w-100 mt-4 mb-0">Add</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
