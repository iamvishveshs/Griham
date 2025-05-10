<?php
require("./check.php");

function clean_input($data, $conn)
{
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Function to return proper redirection URL
function redirect_main($conn)
{
    if (isset($_GET['refferer'])) {
        $get_refferer = clean_input($_GET['refferer'], $conn);
        switch ($get_refferer) {
            case "accomodation":
                return "./browse-listings.php";
            case "roommate":
                    return "./roommate_finder.php";
            case "meal":
                return "./meal-listings.php";
            case "laundry":
                return "./laundry-listings.php";
            case "gym":
                return "./gym-listings.php";
            case "emergency":
                return "./emergency.php";
            default:
                return "./home.php";
        }
    }
    return "./home.php"; // Default fallback
}

// Check if `refferer` is empty
if (!isset($_GET['refferer']) || (isset($_GET['refferer']) && clean_input($_GET['refferer'], $conn) === ''))
{
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Warning",
                    html: "<b>You cannot directly access this page.</b><br>Continue from the dashboard again.",
                    icon: "warning"
                }).then(() => {
                    window.location.href = "./home.php";
                });
            });
          </script>';
    exit();
}

$page_url = redirect_main($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>User Location </title>
    <?php
    require("./style-files.php");
    ?>
    <style>
    .saved-container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        max-width: 100%;
        min-height: 100vh;
        max-height: 100%;

    }

    .search-box {
        gap: 8px;
        margin-bottom: 16px;
        position: relative;
    }

    .search-box input {
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-size: 16px;
        padding: 14px 16px;
        padding-left: 40px;
        border: 1px solid #dbd5ec;
        border-radius: 4px;
        outline: none;

        min-width: 300px;
        width: 100%;
    }

    .search-box input::placeholder {
        color: #aaa;
    }

    .search-box::before {
        content: "\f3c5";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #aaa;
        pointer-events: none;
    }

    .search-box .map-button {
        font-family: "Poppins", sans-serif;
        font-weight: 500;
        font-size: 16px;

        padding: 14px 16px;
        max-width: 155px;
        display: block;
        width: 100%;
        background-color: var(--theme-color);
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .search-box .map-button i {
        font-size: 16px;

    }

    .categories {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        place-items: center;
        gap: 20px;
        margin-top: 40px;
    }

    .category {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        width: 100%;
        height: 108px;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease,
            background-color 0.3s ease, border-color 0.3s ease;
    }

    .category:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .category.selected {
        transform: scale(1.1);
        border: 2px solid;
    }

    @media (max-width: 768px) {
        .saved-container {
            position: relative;
            display: flex;
            justify-content: center;
            max-width: 100%;
            min-height: 100vh;
            max-height: 100%;
            padding: 40px;
            width: 90%;
            margin: 40px auto;
        }

        .categories {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .category {
            max-width: none;
            height: 96px;
        }
    }

    @media (max-width: 480px) {
        .saved-container {
            position: relative;
            display: flex;
            justify-content: center;
            max-width: 100%;
            min-height: 50dvh;
            max-height: 100%;
            padding: 40px;
            width: 90%;
            margin: 40px auto;
        }
        .search-box {
            flex-direction: column;
            gap: 15px;
        }

        .search-box input,
        .search-box .map-button {
            max-width: 100%;
            height: 100%;
            padding: 15px;
        }

        .categories {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .search-box::before {
            display: none;
        }
    }

    .margin-top {
        margin-top: 20px;
    }

    .suggestions {
        position: absolute;
        top: 100%;
        /* Positions it right below input */
        left: 0;
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        display: none;
        z-index: 1000;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }

    .suggestions ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .suggestions li {
        padding: 10px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
        border-bottom: 1px solid #eee;
    }

    .suggestions li:hover {
        background: #f0f0f0;
    }

    /* Ensure the search-box has position:relative so suggestions align properly */
    .search-box {
        position: relative;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .search-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #dbd5ec;
        border-radius: 4px;
        outline: none;
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
</head>

<body>

    <div class="saved-container margin-top">
        <form action="<?php echo $page_url; ?>" method="get" class="location-form">
            <div class="search-box">
                <input type="text" id="location-input" placeholder="Enter Location (atleast 3 characters)"
                    class="search-input" autocomplete="off" name="location">
                <div class="suggestions"></div>
                <button class="map-button" type="submit">
                    Find &nbsp;<i class="fa-solid fa-location-arrow"></i></i>
                </button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $("#location-input").on("input", function() {
            let query = $(this).val().trim();

            if (query.length >= 3) {
                $.ajax({
                    url: "fetch_addresses.php",
                    type: "POST",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        if (data.trim() !== "") {
                            $(".suggestions").html("<ul>" + data + "</ul>").show();
                        } else {
                            $(".suggestions").hide();
                        }
                    }
                });
            } else {
                $(".suggestions").hide();
            }
        });

        $(document).on("click", ".suggestion-item", function() {
            $("#location-input").val($(this).text());
            $(".suggestions").hide();
        });

        $(document).click(function(e) {
            if (!$(e.target).closest(".search-box").length) {
                $(".suggestions").hide();
            }
        });
    });
    </script>


</body>

</html>