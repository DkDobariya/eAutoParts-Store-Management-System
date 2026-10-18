<?php
session_start();
include("config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Honda | Products</title>

  <!-- Bootstrap CSS Only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" href="https://cdn.shopify.com/s/files/1/0415/7846/3390/files/eauto-app-logo_x320.jpg?v=1631018743" type="image/x-icon" />

  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .product-card {
      border: none;
      border-radius: 15px;
      transition: transform 0.3s, box-shadow 0.3s;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .product-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
    }

    .product-image {
      width: 100%;
      height: 200px;
      object-fit: contain;
      background-color: #fff;
      padding: 10px;
    }

    .original-price {
      text-decoration: line-through;
      color: #999;
      font-size: 0.9em;
      margin-left: 6px;
    }

    .stock-status.in {
      color: green;
      font-weight: 600;
    }

    .stock-status.out {
      color: red;
      font-weight: 600;
    }

    .save-tag {
      position: absolute;
      top: 10px;
      left: 10px;
      background-color: #dc3545;
      color: white;
      font-size: 0.75rem;
      padding: 4px 8px;
      border-radius: 5px;
      z-index: 1;
    }

    @media (max-width: 576px) {
      .product-image {
        height: 150px;
      }

      .save-tag {
        font-size: 0.65rem;
        padding: 3px 6px;
      }
    }
  </style>
</head>

<body>

<?php include 'header.php'; ?>

<div class="container py-5">
  <h2 class="text-center mb-5">Honda</h2>

  <?php
  $query = "SELECT * FROM products WHERE main_brand = 'Honda'";
  $result = $conn->query($query);

  if ($result && $result->num_rows > 0):
    $products = [];
    while ($row = $result->fetch_assoc()) {
      $products[] = $row;
    }

    $chunks = array_chunk($products, 4);
    
    foreach ($chunks as $chunk):
  ?>
    <div class="row g-4 mb-4 justify-content-center">
      <?php foreach ($chunk as $row): ?>
        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card product-card position-relative h-100">

            <?php if (!empty($row['original_price']) && $row['original_price'] > $row['price']): ?>
              <div class="save-tag">
                Save ₹<?= $row['original_price'] - $row['price']; ?>
              </div>
            <?php endif; ?>

            <img src="http://localhost:8080/eAuto/Admin/uploads/<?= htmlspecialchars($row['image']); ?>"
                 class="product-image"
                 alt="<?= htmlspecialchars($row['product_name']); ?>">

            <div class="card-body text-center d-flex flex-column justify-content-between">
              <div>
                <h6 class="fw-semibold text-truncate"><?= htmlspecialchars($row['product_name']); ?></h6>
                <p class="text-muted small mb-1"><?= htmlspecialchars($row['main_brand']); ?></p>

                <div class="mb-2">
                  <span class="text-danger fw-bold">₹<?= $row['price']; ?></span>
                  <?php if (!empty($row['original_price'])): ?>
                    <span class="original-price">₹<?= $row['original_price']; ?></span>
                  <?php endif; ?>
                </div>

                <div class="text-warning mb-2">
                  <?= str_repeat('★', floor($row['rating'])) ?>
                  <?= str_repeat('☆', 5 - floor($row['rating'])) ?>
                  <span class="text-dark small"><?= $row['reviews'] ?> reviews</span>
                </div>

                <p class="stock-status <?= ($row['stock_status'] == 'In Stock') ? 'in' : 'out'; ?>">
                  <?= $row['stock_status']; ?>
                </p>
              </div>

              <form action="add_to_cart.php" method="POST">
                <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['product_name']); ?>">
                <input type="hidden" name="price" value="<?= $row['price']; ?>">
                <input type="hidden" name="image" value="<?= htmlspecialchars($row['image']); ?>">
                <input type="hidden" name="main_brand" value="<?= htmlspecialchars($row['main_brand']); ?>">

                <button type="submit"
                        class="btn btn-info text-white w-100 mt-3"
                        <?= ($row['stock_status'] == 'Sold Out') ? 'disabled' : ''; ?>>
                  <?= ($row['stock_status'] == 'Sold Out') ? 'Sold Out' : 'Add to cart'; ?>
                </button>
              </form>
            </div>

          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; else: ?>
    <div class="alert alert-warning text-center">No Honda products found.</div>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS Only -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
