<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nom = cleanInput($_POST["nom"]);
    $prenom = cleanInput($_POST["prenom"]);
    $email = cleanInput($_POST["email"]);
    $description = cleanInput($_POST["description"]);

    $errors = validateForm($nom, $prenom, $email, $_FILES["deposer"], $description);

    if (count($errors) === 0) {
        $to = "support@hackerspoulette.com";
        $subject = "Demande de support";
        $message = "Nom: $nom\n";
        $message .= "Prénom: $prenom\n";
        $message .= "Email: $email\n";
        $message .= "Description: $description\n";
        $headers = "From: $email";

        if (mail($to, $subject, $message, $headers)) {
            echo "Votre demande a été envoyée avec succès. Nous vous contacterons bientôt.";
        } else {
            echo "Désolé, une erreur s'est produite lors de l'envoi de votre demande. Veuillez réessayer plus tard.";
        }
    } else {
        foreach ($errors as $error) {
            echo "$error<br>";
        }
    }
}

function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateForm($nom, $prenom, $email, $file, $description)
{
    $errors = array();

    if (empty($nom) || strlen($nom) < 2 || strlen($nom) > 255) {
        $errors[] = "Veuillez entrer un nom valide (au moins 2 caractères, max 255).";
    }

    if (empty($prenom) || strlen($prenom) < 2 || strlen($prenom) > 255) {
        $errors[] = "Veuillez entrer un prénom valide (au moins 2 caractères, max 255).";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
        $errors[] = "Veuillez entrer une adresse e-mail valide (max 255 caractères).";
    }

    if (!empty($file["name"])) {
        $allowedTypes = array("image/jpeg", "image/png", "image/gif");
        $maxFileSize = 2 * 1024 * 1024; // 2 Mo

        if (!in_array($file["type"], $allowedTypes)) {
            $errors[] = "Veuillez sélectionner un fichier image (jpg, png, gif).";
        }

        if ($file["size"] > $maxFileSize) {
            $errors[] = "La taille du fichier dépasse la limite autorisée (2 Mo).";
        }
    }

    if (empty($description) || strlen($description) < 2 || strlen($description) > 1000) {
        $errors[] = "Veuillez entrer une description valide (au moins 2 caractères, max 1000).";
    }

    return $errors;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hackers Poulette</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <h1>Hackers Poulette</h1>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" required><br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" required><br>

        <label for="email">Adresse e-mail:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="deposer">Déposer un fichier:</label>
        <input type="file" id="deposer" name="deposer"><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="5" cols="40" required></textarea><br>

        <input type="submit" value="Envoyer">
    </form>
</body>

</html>