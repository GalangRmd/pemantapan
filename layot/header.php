<nav class="navbar navbar-expand-lg bg-dark" data-bs-theme="dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link " aria-current="page" href="../dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../kasir/manage_product.php">Manage</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link active" href="">Data Karyawan</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../kasir/transaksi.php">Transaksi</a>
                </li>
                <li class="nav-item <?php echo ($role === 'admin') ? '' : 'd-none'; ?>">
                    <a class="nav-link" href="../activity/log_activity.php">Activity</a>
                </li>
            </div>
        </div>
        <div>
            <form action="../../db/DB_logout.php" method="post">
                <button type="submit" class=" btn btn-danger">Log Out</button>
            </form>
        </div>
    </div>
</nav>