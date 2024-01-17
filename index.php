<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
/* Use a CSS rule to color Sundays red */
.sunday {
    color: red;
    text-align: center;
}
table {
    margin: 0 auto;
    margin-top: 2%;
    width: 50%;
}
.moveform {
    margin-left: 32%;
    margin-top: 5%;
}
.monthtext {
    text-align: center;
}
.changeday{
    background-color:lightgrey;
}
.datetext{
    text-align:center;
}


@media only screen and (max-width: 791px) {

    table {
    margin: 0 auto;
    margin-top: 2%;
    width: 90%;
}

}
    </style>
    <title>CalenderDan.com</title>
</head>
<body>

<?php
function generateCalendar($month, $year) {
    // Use the current year if no year is specified
    if (empty($year)) {
        $year = date('Y'); // Här uppdaterar den nuvarande året
    }
    
    $firstDayOfWeek = date('w', strtotime("$year-$month-01"));
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $monthName = date('F', strtotime("$year-$month-01"));

    // Load name days from 'namnsdag.php'
    include 'namnsdag.php';

    $calendar = '
    <table border="1">
    <tr>
        <th class="monthtext" colspan="8"><h2>' . $monthName . ' ' . $year . '</h2></th>
    </tr>
    <tr class="changeday">
        <th>Sun</th>
        <th>Mon</th>
        <th>Tue</th>
        <th>Wed</th>
        <th>Thu</th>
        <th>Fri</th>
        <th>Sat</th>
        <th>Week</th> <!-- Lägg till th för att visa veckonummer -->
    </tr>
    ';

    // Add empty cells for days before the first day of the month
    for ($i = 0; $i < $firstDayOfWeek; $i++) {
        $calendar .= '<td ></td>';
    }
    $currentDay = $firstDayOfWeek;
    
    

    for ($day = 1; $day <= $daysInMonth; $day++) {
        // This block of code gives Sundays a red color using the "sunday" class, which is styled with CSS
        $sundayClass = ($currentDay == 0) ? 'sunday' : 'datetext';

        // This block of code checks if there is a name for the current date
        $datumKey = date('z', strtotime("$year-$month-$day")) + 1;
        $namn = isset($namnsdag[$datumKey]) ? $namnsdag[$datumKey] :  [''];
        
        $dayNumber = date('z', strtotime("$year-$month-$day")) + 1;
        $days_in_number = isset($namnsdag[$dayNumber]) ? [$dayNumber] :  [''];

        // Get the week number for the current date
        $weekNumber = date('W', strtotime("$year-$month-$day"));

        $calendar .= '<td class="' . $sundayClass . ' ' . $currentDayClass . '"  height="80">' . $day . '<br>'. implode(', ', $namn) . '<br>'. ' Day: (' . implode(', ', $days_in_number). ') '. '<br> W ' . $weekNumber . '</td>';
        $currentDay = ($currentDay + 1) % 7;

        // If it's a Saturday, end the row and start a new one
        if ($currentDay == 0 || $day == $daysInMonth) {
            $calendar .= '</tr>';
            if ($day < $daysInMonth) {
                $calendar .= '<tr>';
            }
        }
    }

    $calendar .= '</table>';
    return $calendar;
}

// Handle button clicks to navigate between months
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
} else {
    $month = date('m');
    $year = date('Y');
}

if (isset($_GET['prev'])) {
    // Go to the previous month
    $timestamp = strtotime("$year-$month-01");
    $prevMonth = date('m', strtotime('-1 month', $timestamp));
    $prevYear = date('Y', strtotime('-1 month', $timestamp));
    $month = $prevMonth;
    $year = $prevYear;
}

if (isset($_GET['next'])) {
    // Go to the next month
    $timestamp = (strtotime("$year-$month-01"));
    $nextMonth = date('m', strtotime('+1 month', $timestamp));
    $nextYear = date('Y', strtotime('+1 month', $timestamp));
    $month = $nextMonth;
    $year = $nextYear;
}

$calendar = generateCalendar($month, $year);
?>

<!-- This form is used to navigate between months -->
<form class="moveform" method="get">
    <input type="submit" name="prev" value="Föregående månad">
    <input type="submit" name="next" value="Nästa månad">
    <input type="hidden" name="month" value="<?php echo $month; ?>">
    <input type="hidden" name="year" value="<?php echo $year; ?>">
    <input type = "date" name = "dat">
    <select name="month" id="monthSelect">
        <?php
        // Create a dropdown menu to select different months
        for ($i = 1; $i <= 12; $i++) {
            $selected = ($i == $month) ? 'selected' : '';
            $monthName = date('F', strtotime("2023-$i-01"));
            echo "<option value='$i' $selected>$monthName</option>";
        }
        ?>
    </select>
    <input type="text" name="year" placeholder="År" value="<?php echo $year; ?>">
    <input type="submit" name="submit" value="Visa">
</form>

<?php
echo $calendar;
?>


