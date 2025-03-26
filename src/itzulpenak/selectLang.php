<form method="post">
    <select name="selectedLang" onchange="this.form.submit()">
        <option value="en" <?php
            if (isset($_POST["selectedLang"]) && $_POST["selectedLang"] == "en") {
                echo "selected";
            } else if (isset($_SESSION["_LANGUAGE"]) && $_SESSION["_LANGUAGE"] == "en") {
                echo "selected";
            }
        ?>>EN</option>

        <option value="eus" <?php
            if (isset($_POST["selectedLang"]) && $_POST["selectedLang"] == "eus") {
                echo "selected";
            } else if (!isset($_POST["selectedLang"]) && isset($_SESSION["_LANGUAGE"]) && $_SESSION["_LANGUAGE"] == "eus") {
                echo "selected";
            }
        ?>>EUS</option>
    </select>
    
</form>
