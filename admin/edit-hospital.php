<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

require_once "include/db.php";




if (
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
) {
    header("Location: hospitals.php");
    exit();
}

$hospital_id = (int) $_GET["id"];



if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $hospital_name = trim($_POST["hospital_name"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $city = trim($_POST["city"]);


    if (
        $hospital_name === "" ||
        $phone === "" ||
        $email === "" ||
        $address === "" ||
        $city === ""
    ) {

        $error = "Please fill in all fields.";

    } else {

        $sql = "UPDATE hospital
                SET Hospital_Name = ?,
                    Phone = ?,
                    Email = ?,
                    Address = ?,
                    City = ?
                WHERE Hospital_ID = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssssi",
            $hospital_name,
            $phone,
            $email,
            $address,
            $city,
            $hospital_id
        );


        if ($stmt->execute()) {

            $stmt->close();

            header("Location: hospitals.php?updated=1");
            exit();

        } else {

            $error = "Unable to update hospital.";

        }

        $stmt->close();
    }
}




$sql = "SELECT
            Hospital_ID,
            Hospital_Name,
            Phone,
            Email,
            Address,
            City,
            Is_Verified
        FROM hospital
        WHERE Hospital_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: hospitals.php");
    exit();

}

$hospital = $result->fetch_assoc();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Hospital | BloodLink Admin</title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

</head>


<body>

<div class="dashboard-container">




    <aside class="sidebar">

        <div class="sidebar-logo">

            <img
                src="assets/image/BloodLink_logo_800px_transparent.webp"
                alt="BloodLink Logo"
            >

        </div>


        <nav class="sidebar-nav">

            <a
                href="dashboard.php"
                class="nav-link"
            >
                Dashboard
            </a>

            <a
                href="hospitals.php"
                class="nav-link active"
            >
                Hospitals
            </a>

            <a
                href="donors.php"
                class="nav-link"
            >
                Donors
            </a>

            <a
                href="events.php"
                class="nav-link"
            >
                Events
            </a>

            <a
                href="reports.php"
                class="nav-link"
            >
                Reports
            </a>

        </nav>


        <div class="sidebar-bottom">

            <a
                href="logout.php"
                class="nav-link logout-link"
            >
                Logout
            </a>

        </div>

    </aside>


    <main class="main-content">


        <header class="top-header">

            <div>

                <h1>Edit Hospital</h1>

                <p>
                    Update registered hospital information
                </p>

            </div>


            <div class="admin-user">
                Admin
            </div>

        </header>


        <section class="dashboard-content">


            <div class="dashboard-section edit-section">


                <div class="section-header">

                    <div>

                        <h2>Hospital Information</h2>

                        <p>
                            Update the selected hospital's details.
                        </p>

                    </div>

                </div>


                <?php if (isset($error)): ?>

                    <div class="form-error">
                        <?php echo htmlspecialchars($error); ?>
                    </div>

                <?php endif; ?>


                <form
                    method="POST"
                    action="edit-hospital.php?id=<?php echo $hospital_id; ?>"
                    class="edit-form"
                >


                    <div class="form-grid">


                        <!-- Hospital ID -->

                        <div class="form-group">

                            <label>
                                Hospital ID
                            </label>

                            <input
                                type="text"
                                value="<?php echo htmlspecialchars($hospital["Hospital_ID"]); ?>"
                                disabled
                            >

                        </div>


                        <!-- Hospital Name -->

                        <div class="form-group">

                            <label for="hospital_name">
                                Hospital Name
                            </label>

                            <input
                                type="text"
                                id="hospital_name"
                                name="hospital_name"
                                value="<?php echo htmlspecialchars($hospital["Hospital_Name"]); ?>"
                                required
                            >

                        </div>


                        <!-- Phone -->

                        <div class="form-group">

                            <label for="phone">
                                Phone
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="<?php echo htmlspecialchars($hospital["Phone"]); ?>"
                                required
                            >

                        </div>


                      

                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($hospital["Email"]); ?>"
                                required
                            >

                        </div>


                        

                        <div class="form-group">

                            <label for="city">
                                City
                            </label>

                            <input
                                type="text"
                                id="city"
                                name="city"
                                value="<?php echo htmlspecialchars($hospital["City"]); ?>"
                                required
                            >

                        </div>


                       

                        <div class="form-group form-full">

                            <label for="address">
                                Address
                            </label>

                            <textarea
                                id="address"
                                name="address"
                                rows="4"
                                required
                            ><?php echo htmlspecialchars($hospital["Address"]); ?></textarea>

                        </div>


                        

                        <div class="form-group">

                            <label>
                                Verification Status
                            </label>

                            <input
                                type="text"
                                value="<?php echo ($hospital["Is_Verified"] == 1) ? "Approved" : "Pending"; ?>"
                                disabled
                            >

                        </div>


                    </div>


                    <div class="form-actions">

                        <a
                            href="hospitals.php"
                            class="secondary-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="primary-btn"
                        >
                            Update Hospital
                        </button>

                    </div>


                </form>


            </div>

        </section>

    </main>

</div>

</body>

</html>