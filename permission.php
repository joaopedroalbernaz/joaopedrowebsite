<?php

function calculatePermission($read, $write, $execute)
{
    $permission = 0;
    if ($read) {
        $permission += 4;
    }
    if ($write) {
        $permission += 2;
    }
    if ($execute) {
        $permission += 1;
    }
    return $permission;
}

function printPermission($permission, $label)
{
    $symbolic = '';
    $symbolic .= ($permission & 4) ? 'r' : '-';
    $symbolic .= ($permission & 2) ? 'w' : '-';
    $symbolic .= ($permission & 1) ? 'x' : '-';
    echo "<div><strong>$label:</strong> $symbolic</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_read = isset($_POST['user_read']) ? 1 : 0;
    $user_write = isset($_POST['user_write']) ? 1 : 0;
    $user_execute = isset($_POST['user_execute']) ? 1 : 0;

    $group_read = isset($_POST['group_read']) ? 1 : 0;
    $group_write = isset($_POST['group_write']) ? 1 : 0;
    $group_execute = isset($_POST['group_execute']) ? 1 : 0;

    $others_read = isset($_POST['others_read']) ? 1 : 0;
    $others_write = isset($_POST['others_write']) ? 1 : 0;
    $others_execute = isset($_POST['others_execute']) ? 1 : 0;

    $user_permission = calculatePermission($user_read, $user_write, $user_execute);
    $group_permission = calculatePermission($group_read, $group_write, $group_execute);
    $others_permission = calculatePermission($others_read, $others_write, $others_execute);

    $permission = $user_permission * 100 + $group_permission * 10 + $others_permission;
} else {
    $permission = 755; // Default numeric permission value
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Linux Permission Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-top: 50px;
        }

        h1 {
            text-align: center;
        }

        form {
            margin-top: 30px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        input[type="text"] {
            width: 50px;
        }

        .chmod-value {
            margin-top: 30px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .permission-line {
            display: flex;
            justify-content: space-between;
        }

        .permission-line .permission-item {
            flex: 1;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Linux Permission Calculator</h1>

        <form id="permissionForm">
            <label>User:</label>
            <input type="checkbox" name="user_read" value="4" <?php if ($user_permission & 4) echo 'checked'; ?>> Read
            <input type="checkbox" name="user_write" value="2" <?php if ($user_permission & 2) echo 'checked'; ?>> Write
            <input type="checkbox" name="user_execute" value="1" <?php if ($user_permission & 1) echo 'checked'; ?>> Execute
            <br>

            <label>Group:</label>
            <input type="checkbox" name="group_read" value="4" <?php if ($group_permission & 4) echo 'checked'; ?>> Read
            <input type="checkbox" name="group_write" value="2" <?php if ($group_permission & 2) echo 'checked'; ?>> Write
            <input type="checkbox" name="group_execute" value="1" <?php if ($group_permission & 1) echo 'checked'; ?>> Execute
            <br>

            <label>Others:</label>
            <input type="checkbox" name="others_read" value="4" <?php if ($others_permission & 4) echo 'checked'; ?>> Read
            <input type="checkbox" name="others_write" value="2" <?php if ($others_permission & 2) echo 'checked'; ?>> Write
            <input type="checkbox" name="others_execute" value="1" <?php if ($others_permission & 1) echo 'checked'; ?>> Execute
            <br>

            <label>Numeric:</label>
            <input type="text" name="numeric_permission" id="numericPermission" value="<?= $permission ?>">
            <br>

            <label>Symbolic:</label>
            <input type="text" name="symbolic_permission" id="symbolicPermission" readonly>

        </form>

    </div>

    <script>
        const permissionForm = document.getElementById('permissionForm');
        const numericPermissionInput = document.getElementById('numericPermission');
        const symbolicPermissionInput = document.getElementById('symbolicPermission');
        const permissionFields = document.querySelector('.permission-line');

        function updateNumericPermission() {
            let numericPermission = 0;

            numericPermission += (permissionForm.elements['user_read'].checked ? 4 : 0);
            numericPermission += (permissionForm.elements['user_write'].checked ? 2 : 0);
            numericPermission += (permissionForm.elements['user_execute'].checked ? 1 : 0);

            numericPermission += (permissionForm.elements['group_read'].checked ? 40 : 0);
            numericPermission += (permissionForm.elements['group_write'].checked ? 20 : 0);
            numericPermission += (permissionForm.elements['group_execute'].checked ? 10 : 0);

            numericPermission += (permissionForm.elements['others_read'].checked ? 4 : 0);
            numericPermission += (permissionForm.elements['others_write'].checked ? 2 : 0);
            numericPermission += (permissionForm.elements['others_execute'].checked ? 1 : 0);

            numericPermissionInput.value = numericPermission;
        }

        function updateSymbolicPermission() {
            const numericPermission = numericPermissionInput.value;
            const userPermission = {
                read: (numericPermission & 400) !== 0,
                write: (numericPermission & 200) !== 0,
                execute: (numericPermission & 100) !== 0
            };

            const groupPermission = {
                read: (numericPermission & 40) !== 0,
                write: (numericPermission & 20) !== 0,
                execute: (numericPermission & 10) !== 0
            };

            const othersPermission = {
                read: (numericPermission & 4) !== 0,
                write: (numericPermission & 2) !== 0,
                execute: (numericPermission & 1) !== 0
            };

            permissionForm.elements['user_read'].checked = userPermission.read;
            permissionForm.elements['user_write'].checked = userPermission.write;
            permissionForm.elements['user_execute'].checked = userPermission.execute;

            permissionForm.elements['group_read'].checked = groupPermission.read;
            permissionForm.elements['group_write'].checked = groupPermission.write;
            permissionForm.elements['group_execute'].checked = groupPermission.execute;

            permissionForm.elements['others_read'].checked = othersPermission.read;
            permissionForm.elements['others_write'].checked = othersPermission.write;
            permissionForm.elements['others_execute'].checked = othersPermission.execute;
        }

        permissionForm.addEventListener('change', function () {
            updateNumericPermission();
            updateSymbolicPermission();

            const userPermission = calculatePermission(
                permissionForm.elements['user_read'].checked,
                permissionForm.elements['user_write'].checked,
                permissionForm.elements['user_execute'].checked
            );

            const groupPermission = calculatePermission(
                permissionForm.elements['group_read'].checked,
                permissionForm.elements['group_write'].checked,
                permissionForm.elements['group_execute'].checked
            );

            const othersPermission = calculatePermission(
                permissionForm.elements['others_read'].checked,
                permissionForm.elements['others_write'].checked,
                permissionForm.elements['others_execute'].checked
            );

            const symbolicPermission = printPermission(userPermission, 'User') +
                printPermission(groupPermission, 'Group') +
                printPermission(othersPermission, 'Others');

            symbolicPermissionInput.value = symbolicPermission;
        });

        updateNumericPermission();
        updateSymbolicPermission();
    </script>
</body>
</html>
