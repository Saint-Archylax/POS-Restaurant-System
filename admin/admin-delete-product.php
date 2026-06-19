<?php include('includes/header.php') ?>


    <!--remixions link-->
    <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
    rel="stylesheet"/>

    <!--awesomeicons link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

    <!--google fonts link-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../home.css">



<!--Products to delete-->

<div class="row">
  <div class="ms-3">
    <h3 class="mb-0 h4 font-weight-bolder">Delete Product</h3>
    <p class="mb-4">
      Delete items as needed!
    </p>
  </div>


<div class="update-container" id="update-container">
    <div class="card-lists"> 
        <div class="card">
            <img src="../img/lumpia.jpg"/>
            <h5 class="food-name">Lumpia</h5>
            <br>
            <div class="card-price">
                <div class="price">&#8369 7.00</div>
                <label for="active-edit" id="icon-delete"><i class="ri-delete-bin-6-line"></i> Delete</label>
            </div>
        </div>
        
        <div class="card">
            <img src="../img/lechonkawali.jpg"/>
            <h5 class="food-name">Lechon Kawali</h5>
            <br>
            <div class="card-price">
                <div class="price">&#8369 70.00</div>
                <label for="active-edit" id="icon-delete"><i class="ri-delete-bin-6-line"></i> Delete</label>
            </div>
        </div>
    
        <div class="card">
            <img src="../img/adobo.jpg"/>
            <h5 class="food-name">Pork Adobo</h5>
            <br>
            <div class="card-price">
                <div class="price">&#8369 50.00</div>
                <label for="active-edit" id="icon-delete"><i class="ri-delete-bin-6-line"></i> Delete</label>
            </div>
        </div>
    
        <div class="card">
            <img src="../img/lecheFlan.jpg"/>
            <h5 class="food-name">Leche Flan</h5>
            <br>
            <div class="card-price">
                <div class="price">&#8369 60.00</div>
                <label for="active-edit" id="icon-delete"><i class="ri-delete-bin-6-line"></i> Delete</label>
            </div>
        </div>
    
        <div class="card">
            <img src="../img/porkBBQ.jpeg"/>
            <h5 class="food-name">Pork BBQ</h5>
            <br>
            <div class="card-price">
                <div class="price">&#8369 30.00</div>
                <label for="active-edit" id="icon-delete"><i class="ri-delete-bin-6-line"></i> Delete</label>
            </div>
        </div>
    
        <div class="card">
            <img src="../img/porkSisig.jpg"/>
            <h5 class="food-name">Pork Sisig</h5>
            <br>
            <div class="card-price">
                <div class="price">&#8369 70.00</div>
                <label for="active-edit" id="icon-delete"><i class="ri-delete-bin-6-line"></i> Delete</label>
            </div>
        </div>
    
        <div class="card">
            <img src="../img/crispyPata.jpg"/>
            <h5 class="food-name">Crispy Pata</h5>
            <br>
            <div class="card-price">
                <div class="price">&#8369 90.00</div>
                <label for="active-edit" id="icon-delete"><i class="ri-delete-bin-6-line"></i> Delete</label>
            </div>
        </div>
    </div>
</div>


<!--Confirm delete-->
<input type="checkbox" id="active-edit">
<div class="edit-container">
    <table class="table-delete">
        <tr>
            <td style="padding-top: 20px; padding-right: 20px;" colspan="2">
                <h5 style="font-weight: 400;">Are you sure you want to delete this product?</h5>
            </td>
        </tr>
        <tr>
            <td align="center">
                <div class="css-yes">
                    <a href="">Yes</a>
                </div>
            </td>

            <td align="center">
                <div class="css-no">
                    <a href="">No</a>
                </div>
            </td>
        </tr>


    </table>
</div>

<?php include('includes/footer.php') ?>