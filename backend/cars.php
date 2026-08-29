<?php
include("backend/db.php"); // adjust path if needed

$cars = $conn->query("SELECT * FROM cars WHERE status='Available' ORDER BY id DESC");
?>
<div class="cars-list">
<?php if($cars->num_rows > 0): ?>
    <?php while($car = $cars->fetch_assoc()): ?>
        <div class="car-card">
            <img src="<?= $car['image'] ?>" alt="<?= $car['name'] ?>">
            <h3><?= htmlspecialchars($car['name']." (".$car['year'].")") ?></h3>
            <p>Type: <?= htmlspecialchars($car['type']) ?></p>
            <p>Transmission: <?= htmlspecialchars($car['transmission']) ?></p>
            <p>Seats: <?= $car['seats'] ?></p>
            <p>Fuel: <?= htmlspecialchars($car['fuel']) ?></p>
            <p>Price: ₹<?= $car['price'] ?>/day</p>
            <a href="booking.php?car_id=<?= $car['id'] ?>" class="btn-book">Book Now</a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No cars available right now.</p>
<?php endif; ?>
</div>
<style>
    .cars-list {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

.car-card {
    width: 300px;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    padding: 15px;
    text-align: center;
    background-color: #fff;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
}

.car-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
}

.car-card h3 {
    margin: 10px 0;
}

.btn-book {
    display: inline-block;
    padding: 8px 16px;
    background-color: #0046FF;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 10px;
}

</style>