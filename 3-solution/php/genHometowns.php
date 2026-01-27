<?php

$hometowns = array(
    'Cerulean City',
    'Celadon City',
    'Cinnabar Island',
    'Fuchsia City',
    'Indigo Plateau',
    'Lavender Town',
    'Pallet Town',
    'Petalburg City',
    'Pewter City',
    'Saffron City',
    'Twinleaf Town',
    'Vermilion City',
    'Viridian City',
);

foreach ($hometowns as $town) {
    echo ('<option value="' . $town . '">');
}
