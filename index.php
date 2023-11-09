<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Använd en CSS-regel för att färga söndagar röda */
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

/* td {
    color:lightblue;
} */

/* .current-day {
    background-color: lightyellow;
} */


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
    // Använd det nuvarande året om inget år har angivits
    if (empty($year)) {
        $year = date('Y'); // Här uppdaterar den nuvarande året
    }
    
    $firstDayOfWeek = date('w', strtotime("$year-$month-01"));
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $monthName = date('F', strtotime("$year-$month-01"));

    // Ladda in namnsdagar från 'namnsdag.php'
    include 'namnsdag.php';

    // Denna hämtar det aktuella datumet
    // $currentDate = date('j');

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

    // Här läggs till tomma celler för dagar före den första dagen i månaden
    for ($i = 0; $i < $firstDayOfWeek; $i++) {
        $calendar .= '<td ></td>';
    }
    $currentDay = $firstDayOfWeek;
    
    

    for ($day = 1; $day <= $daysInMonth; $day++) {
        // Denna block av kod ska ge söndagar röd färg. "sunday" är för söndagar och är röd med hjälp av CSS (external).
        $sundayClass = ($currentDay == 0) ? 'sunday' : 'datetext';

        // Denna block av kod ska kontrollera om det finns ett namn för det aktuella datumet
        $datumKey = date('z', strtotime("$year-$month-$day")) + 1;
        $namn = isset($namnsdag[$datumKey]) ? $namnsdag[$datumKey] :  [''];
        
        $dayNumber = date('z', strtotime("$year-$month-$day")) + 1;
        $days_in_number = isset($namnsdag[$dayNumber]) ? [$dayNumber] :  [''];

        // Hämta veckonumret för det aktuella datumet
        $weekNumber = date('W', strtotime("$year-$month-$day"));

        // Lägg till CSS-klassen "current-day" om dagen är den aktuella dagen
        // $currentDayClass = ($day == $currentDate) ? 'current-day' : '';

        $calendar .= '<td class="' . $sundayClass . ' ' . $currentDayClass . '"  height="80">' . $day . '<br>'. implode(', ', $namn) . '<br>'. ' Day: (' . implode(', ', $days_in_number). ') '. '<br> W ' . $weekNumber . '</td>';
        $currentDay = ($currentDay + 1) % 7;

        // Om det är en lördag så avslutar raden och börjar i en ny rad
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

// Här hanteras knapptryckningar för att kunna navigera
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];
} else {
    $month = date('m');
    $year = date('Y');
}

if (isset($_GET['prev'])) {
    // Denna går till föregående månad
    $timestamp = strtotime("$year-$month-01");
    $prevMonth = date('m', strtotime('-1 month', $timestamp));
    $prevYear = date('Y', strtotime('-1 month', $timestamp));
    $month = $prevMonth;
    $year = $prevYear;
}

if (isset($_GET['next'])) {
    // Denna går till nästa månad
    $timestamp = (strtotime("$year-$month-01"));
    $nextMonth = date('m', strtotime('+1 month', $timestamp));
    $nextYear = date('Y', strtotime('+1 month', $timestamp));
    $month = $nextMonth;
    $year = $nextYear;
}

$calendar = generateCalendar($month, $year);
?>

<!-- Denna formulär är till för att kunna navigera mellan månader -->
<form class="moveform" method="get">
    <input type="submit" name="prev" value="Föregående månad">
    <input type="submit" name="next" value="Nästa månad">
    <input type="hidden" name="month" value="<?php echo $month; ?>">
    <input type="hidden" name="year" value="<?php echo $year; ?>">
    <input type = "date" name = "dat">
    <select name="month" id="monthSelect">
        <?php
        // Här skapas en dropdown-meny för att kunna välja olika månader
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



<?php 

// include 'namnsdag.php';

// echo date('z') + 1;

// foreach ($namnsdag as $datum => [$namn]) {
// echo " $datum $namn <br> ";
// // echo "Namnsdag för $datum är $namn.";
// // echo $namnsdag[date('z')];

// }
?>

</body>
</html>

<!-- 
- isset är en inbyggd PHP-funktion som används för att kontrollera om en variabel är satt och inte är null. Den returnerar true 
om variabeln är satt och har ett värde, och false om variabeln inte är satt eller är null. Det är användbart för att undvika 
fel när du försöker använda variabler som inte har tilldelats något värde. 
-->

<!-- 
- empty är en annan inbyggd PHP-funktion som används för att kontrollera om en variabel är tom. Variabeln anses vara tom 
om den är en av följande:

1. En sträng som är tom.
2. En sträng som innehåller enbart mellanslag.
3. En array som är tom.
4. En variabel som är satt till null. 
-->



<!-- Steg 1 -->

<!-- <form method="get">
  <input type="submit" name="prev" value="Föregående månad">
  <input type="submit" name="next" value="Nästa månad">
  <input type="hidden" name="month" value="<?php  ?>">echo $month;
  <input type="hidden" name="year" value="<?php  ?>">echo $year;
</form> -->
<?php

// function generateCalendar($month, $year) {
//   $firstDayOfWeek = date('w', strtotime("$year-$month-01"));
//   $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//   $monthName = date('F', strtotime("$year-$month-01"));


//   $calendar .= 
//   '<table width="70%" border="5">
//   <tr>
//       <th colspan="7">' . $monthName . ' ' . $year . '</th>
//      </tr>
//     <tr>
//       <th>Sun</th>
//       <th>Mon</th>
//       <th>Tue</th>
//       <th>Wed</th>
//       <th>Thu</th>
//       <th>Fri</th>
//       <th>Sat</th>
//     </tr>';
//   $currentDay = $firstDayOfWeek;

//   for ($day = 1; $day <= $daysInMonth; $day++) {
//     $currentDay == 0 && $calendar .= '<tr>';
//     $calendar .= "<td height = 80>$day</td>";
//     $currentDay == 6 && $calendar .= '</tr>';
//     $currentDay = ($currentDay + 1) % 7;
//   }

//   $calendar .= '</table>';
//   return $calendar;
// }

// $month = date('m');
// $year = date('Y');
// $calendar = generateCalendar($month, $year);
// echo $calendar;
?>

<!-- <form method="get">
  <input type="submit" name="prev" value="Föregående månad">
  <input type="submit" name="next" value="Nästa månad">
  <input type="hidden" name="month" value="<?php  ?>">echo $month;
  <input type="hidden" name="year" value="<?php  ?>">echo $year;
</form> -->
<?php

// if (isset($_GET['prev'])) {
//   // Om "Föregående månad" knappen klickades
//   $month--;
//   if ($month == 0) {
//     $month = 12;
//     $year--;
//   }
// } elseif (isset($_GET['next'])) {
//   // Om "Nästa månad" knappen klickades
//   $month++;
//   if ($month == 13) {
//     $month = 1;
//     $year++;
//   }
// }

// function generateCalendar($month, $year) {
//   $firstDayOfWeek = date('w', strtotime("$year-$month-01"));
//   $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//   $monthName = date('F', strtotime("$year-$month-01"));

//   $calendar = 
//   '<table width="70%" border="5">
//   <tr>
//       <th colspan="7">' . $monthName . ' ' . $year . '</th>
//      </tr>
//     <tr>
//       <th>Sun</th>
//       <th>Mon</th>
//       <th>Tue</th>
//       <th>Wed</th>
//       <th>Thu</th>
//       <th>Fri</th>
//       <th>Sat</th>
//     </tr>';
//   $currentDay = $firstDayOfWeek;

//   for ($day = 1; $day <= $daysInMonth; $day++) {
//     $currentDay == 0 && $calendar .= '<tr>';
//     $calendar .= "<td height = 80>$day</td>";
//     $currentDay == 6 && $calendar .= '</tr>';
//     $currentDay = ($currentDay + 1) % 7;
//   }

//   $calendar .= '</table>';
//   return $calendar;
// }

// $month = date('m');
// $year = date('Y');
// $calendar = generateCalendar($month, $year);
// echo $calendar;
?>



<!-- <form method="get">
  <input type="submit" name="prev" value="Föregående månad">
  <input type="submit" name="next" value="Nästa månad">
  <input type="hidden" name="month" value="<?php  ?>">echo $month;
  <input type="hidden" name="year" value="<?php  ?>">echo $year;
</form> -->
<?php

// function generateCalendar($month, $year) {
//   if (isset($_GET['prev'])) {
//     // Om "Föregående månad" knappen klickades
//     $prevMonth = $month - 1;
//     if ($prevMonth == 0) {
//       $prevMonth = 12;
//       $year--;
//     }
//     $month = $prevMonth;
//   } elseif (isset($_GET['next'])) {
//     // Om "Nästa månad" knappen klickades
//     $nextMonth = $month + 1;
//     if ($nextMonth == 12) {
//       $nextMonth = 1;
//       $year++;
//     }
//     $month = $nextMonth;

//   }

//   $firstDayOfWeek = date('w', strtotime("$year-$month-01"));
//   $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
//   $monthName = date('F', strtotime("$year-$month-01"));

//   $calendar = 
//   '<table width="70%" border="5">
//     <tr>
//       <th colspan="7">' . $monthName . ' ' . $year . '</th>
//     </tr>
//     <tr>
//       <th>Sun</th>
//       <th>Mon</th>
//       <th>Tue</th>
//       <th>Wed</th>
//       <th>Thu</th>
//       <th>Fri</th>
//       <th>Sat</th>
//     </tr>

//     ';
//   $currentDay = $firstDayOfWeek;

//   for ($day = 1; $day <= $daysInMonth; $day++) {
//     $currentDay == 0 && $calendar .= '<tr>';
//     $calendar .= "<td height = 80>$day</td>";
//     $currentDay == 6 && $calendar .= '</tr>';
//     $currentDay = ($currentDay + 1) % 7;
//   }

//   $calendar .= '</table>';
//   return $calendar;
// }

// $month = date('m');
// $year = date('Y');
// $calendar = generateCalendar($month, $year);
// echo $calendar;
?>



