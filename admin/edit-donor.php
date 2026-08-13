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
    header("Location: donors.php");
    exit();
}

$donor_id = (int) $_GET["id"];

$error = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = trim($_POST["full_name"]);
    $username = trim($_POST["username"]);
    $blood_group = trim($_POST["blood_group"]);
    $date_of_birth = trim($_POST["date_of_birth"]);
    $gender = trim($_POST["gender"]);
    $address = trim($_POST["address"]);
    $email = trim($_POST["email"]);
    $phone_number = trim($_POST["phone_number"]);
    $last_donation_date = trim($_POST["last_donation_date"]);


    if (
        $full_name === "" ||
        $username === "" ||
        $blood_group === "" ||
        $date_of_birth === "" ||
        $gender === ""
    ) {

        $error = "Please fill in all required fields.";

    } else {


        if ($last_donation_date === "") {
            $last_donation_date = null;
        }


        $sql = "UPDATE donor
                SET Full_Name = ?,
                    Username = ?,
                    Blood_Group = ?,
                    Date_Of_Birth = ?,
                    Gender = ?,
                    Address = ?,
                    Email = ?,
                    Phone_Number = ?,
                    Last_Donation_Date = ?
                WHERE Donor_ID = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sssssssssi",
            $full_name,
            $username,
            $blood_group,
            $date_of_birth,
            $gender,
            $address,
            $email,
            $phone_number,
            $last_donation_date,
            $donor_id
        );


        if ($stmt->execute()) {

            $stmt->close();

            header("Location: donors.php?updated=1");
            exit();

        } else {

            $error = "Unable to update donor.";

        }

        $stmt->close();

    }

}



$sql = "SELECT
            Donor_ID,
            Full_Name,
            Username,
            Blood_Group,
            Date_Of_Birth,
            Gender,
            Address,
            Email,
            Phone_Number,
            Last_Donation_Date
        FROM donor
        WHERE Donor_ID = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $donor_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows !== 1) {

    $stmt->close();

    header("Location: donors.php");
    exit();

}

$donor = $result->fetch_assoc();

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

    <title>Edit Donor | BloodLink Admin</title>

    <link
        rel="stylesheet"
        href="assets/css/admin.css"
    >

</head>


<body>

<div class="dashboard-container">


    <!-- Sidebar -->

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
                class="nav-link"
            >
                Hospitals
            </a>

            <a
                href="donors.php"
                class="nav-link active"
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


    <!-- Main Content -->

    <main class="main-content">


        <header class="top-header">

            <div>

                <h1>Edit Donor</h1>

                <p>
                    Update registered donor information
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

                        <h2>Donor Information</h2>

                        <p>
                            Update the selected donor's details.
                        </p>

                    </div>

                </div>


                <?php if ($error !== ""): ?>

                    <div class="form-error">

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </div>

                <?php endif; ?>


                <form
                    method="POST"
                    action="edit-donor.php?id=<?php echo $donor_id; ?>"
                    class="edit-form"
                >


                    <div class="form-grid">


                        <!-- Donor ID -->

                        <div class="form-group">

                            <label>
                                Donor ID
                            </label>

                            <input
                                type="text"
                                value="<?php echo htmlspecialchars($donor["Donor_ID"]); ?>"
                                disabled
                            >

                        </div>


                        <!-- Full Name -->

                        <div class="form-group">

                            <label for="full_name">
                                Full Name
                            </label>

                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                value="<?php echo htmlspecialchars($donor["Full_Name"]); ?>"
                                required
                            >

                        </div>


                        <!-- Username -->

                        <div class="form-group">

                            <label for="username">
                                Username
                            </label>

                            <input
                                type="text"
                                id="username"
                                name="username"
                                value="<?php echo htmlspecialchars($donor["Username"]); ?>"
                                required
                            >

                        </div>


                        <!-- Blood Group -->

                        <div class="form-group">

                            <label for="blood_group">
                                Blood Group
                            </label>

                            <select
                                id="blood_group"
                                name="blood_group"
                                required
                            >

                                <?php

                                $blood_groups = [
                                    "A+",
                                    "A-",
                                    "B+",
                                    "B-",
                                    "AB+",
                                    "AB-",
                                    "O+",
                                    "O-"
                                ];

                                foreach ($blood_groups as $group):

                                ?>

                                    <option
                                        value="<?php echo $group; ?>"
                                        <?php
                                        echo (
                                            $donor["Blood_Group"] === $group
                                        )
                                            ? "selected"
                                            : "";
                                        ?>
                                    >
                                        <?php echo $group; ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- Date of Birth -->

                        <div class="form-group">

                            <label for="date_of_birth">
                                Date of Birth
                            </label>

                            <input
                                type="date"
                                id="date_of_birth"
                                name="date_of_birth"
                                value="<?php echo htmlspecialchars($donor["Date_Of_Birth"]); ?>"
                                required
                            >

                        </div>


                        <!-- Gender -->

                        <div class="form-group">

                            <label for="gender">
                                Gender
                            </label>

                            <select
                                id="gender"
                                name="gender"
                                required
                            >

                                <option
                                    value="Male"
                                    <?php
                                    echo (
                                        $donor["Gender"] === "Male"
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Male
                                </option>

                                <option
                                    value="Female"
                                    <?php
                                    echo (
                                        $donor["Gender"] === "Female"
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Female
                                </option>

                                <option
                                    value="Other"
                                    <?php
                                    echo (
                                        $donor["Gender"] === "Other"
                                    )
                                        ? "selected"
                                        : "";
                                    ?>
                                >
                                    Other
                                </option>

                            </select>

                        </div>


                        <!-- Email -->

                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($donor["Email"] ?? ""); ?>"
                            >

                        </div>


                        <!-- Phone -->

                        <div class="form-group">

                            <label for="phone_number">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                id="phone_number"
                                name="phone_number"
                                value="<?php echo htmlspecialchars($donor["Phone_Number"] ?? ""); ?>"
                            >

                        </div>


                        <!-- Address -->

                        <div class="form-group form-full">

                            <label for="address">
                                Address
                            </label>

                            <textarea
                                id="address"
                                name="address"
                                rows="4"
                            ><?php echo htmlspecialchars($donor["Address"] ?? ""); ?></textarea>

                        </div>


                        <!-- Last Donation Date -->

                        <div class="form-group">

                            <label for="last_donation_date">
                                Last Donation Date
                            </label>

                            <input
                                type="date"
                                id="last_donation_date"
                                name="last_donation_date"
                                value="<?php echo htmlspecialchars($donor["Last_Donation_Date"] ?? ""); ?>"
                            >

                        </div>


                    </div>


                    <div class="form-actions">

                        <a
                            href="donors.php"
                            class="secondary-btn"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="primary-btn"
                        >
                            Update Donor
                        </button>

                    </div>


                </form>


            </div>

        </section>

    </main>

</div>

</body>

</html>