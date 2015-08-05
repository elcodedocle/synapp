<?php
/**
 * @param string $user
 * @return string
 */
function process_avatar($user)
{

    if (!is_file($_FILES['avatar']['tmp_name'])) {

        return "";

    } else {

        if (
            (($_FILES['avatar']['type'] == "image/gif")
                || ($_FILES['avatar']['type'] == "image/jpeg")
                || ($_FILES['avatar']['type'] == "image/jpg")
                || ($_FILES['avatar']['type'] == "image/x-png")
                || ($_FILES['avatar']['type'] == "image/png")
                || ($_FILES['avatar']['type'] == "image/pjpeg"))
            && ($_FILES['avatar']['size'] < MAX_AVATAR_SIZE_BYTES)
        ) {

            if ($_FILES['avatar']['error'] > 0) {

                return ("error1");

            } else {

                $extension = str_replace('x-', '', str_replace('image/', '', $_FILES['avatar']['type']));
                $newfilename = $user . "." . $extension;
                move_uploaded_file(
                    $_FILES['avatar']['tmp_name'],
                    dirname(__FILE__) . "/../uploads/avatars/" . $newfilename
                );
                return $newfilename;

            }

        } else {

            return ("error2"); //("Avatar Error: MAX 290KB, gif/png/jpeg<br />");

        }

    }

}
