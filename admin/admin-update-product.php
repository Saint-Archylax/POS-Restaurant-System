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


    <!--Products to edit-->
    <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Update Product</h3>
        <p class="mb-4">
        Update your products!
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
                    <label for="active-edit"><i class="fa-regular fa-pen-to-square" id="edit-icon"> Edit</i></label>
                </div>
            </div>
            
            <div class="card">
                <img src="../img/lechonkawali.jpg"/>
                <h5 class="food-name">Lechon</h5>
                <br>
                <div class="card-price">
                    <div class="price">&#8369 70.00</div>
                    <label for="active-edit"><i class="fa-regular fa-pen-to-square" id="edit-icon"> Edit</i></label>
                </div>
            </div>
        
            <div class="card">
                <img src="../img/adobo.jpg"/>
                <h5 class="food-name">Pork Adobo</h5>
                <br>
                <div class="card-price">
                    <div class="price">&#8369 50.00</div>
                    <label for="active-edit"><i class="fa-regular fa-pen-to-square" id="edit-icon"> Edit</i></label>
                </div>
            </div>
        
            <div class="card">
                <img src="../img/lecheFlan.jpg"/>
                <h5 class="food-name">Leche Flan</h5>
                <br>
                <div class="card-price">
                    <div class="price">&#8369 60.00</div>
                    <label for="active-edit"><i class="fa-regular fa-pen-to-square" id="edit-icon"> Edit</i></label>
                </div>
            </div>
        
            <div class="card">
                <img src="../img/porkBBQ.jpeg"/>
                <h5 class="food-name">Pork BBQ</h5>
                <br>
                <div class="card-price">
                    <div class="price">&#8369 30.00</div>
                    <label for="active-edit"><i class="fa-regular fa-pen-to-square" id="edit-icon"> Edit</i></label>
                </div>
            </div>
        
            <div class="card">
                <img src="../img/porkSisig.jpg"/>
                <h5 class="food-name">Pork Sisig</h5>
                <br>
                <div class="card-price">
                    <div class="price">&#8369 70.00</div>
                    <label for="active-edit"><i class="fa-regular fa-pen-to-square" id="edit-icon"> Edit</i></label>
                </div>
            </div>
        
            <div class="card">
                <img src="../img/crispyPata.jpg"/>
                <h5 class="food-name">Crispy Pata</h5>
                <br>
                <div class="card-price">
                    <div class="price">&#8369 90.00</div>
                    <label for="active-edit"><i class="fa-regular fa-pen-to-square" id="edit-icon"> Edit</i></label>
                </div>
            </div>
        </div>
    </div>


<!--Update Product-->
<input type="checkbox" id="active-edit">
<div class="edit-container">
    <table class="table-update">
        <tr>
            <td style="padding-top: 0; padding-right: 0;">
                <div class="x" style="padding: 20px 20px 0 20px;">
                    <label for="active-edit"><i class="fa-solid fa-xmark fa-xl"></i></label>
                </div>
                <div style="padding: 0 20px 0 20px;">
                    <h5 style="font-weight: 400;">Enter the updated details of your product.</h5>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="padding: 0 20px 0 20px;">
                    <span class="input-label">New Name:</span> <br>
                    <input type="text" placeholder="Product Name" class="input-add" required> 
                </div>
            </td>
        </tr>

        <tr>
            <td>
                <div style="padding: 0 20px 0 20px;">
                    <span class="input-label">New Price:</span> <br>
                    <input type="number" placeholder="Price" class="input-add" required>
                </div>
            </td>
        </tr>
        
        <tr>
            <td>
                <div style="padding: 0 20px 0 20px;">
                    <span class="input-label">New picture:</span> <br>
                    <input type="file" name="product_image" accept="image/*" required style="font-size: 15px;">
                </div>
            </td>
        </tr>

        <tr>
            <div>
                <td style="padding: 20px;"><button id="update-product-btn">Update</button></td>
            </div>
        </tr>


    </table>
</div>

<?php include('includes/footer.php') ?>