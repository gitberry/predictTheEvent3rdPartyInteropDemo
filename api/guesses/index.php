<?php
header('Content-Type: application/json');

$path = __DIR__ . "/../../data/";
$fileName = $path . "guesses.json";
if (file_exists($fileName)) {
    readfile($fileName);
    exit; 
}

//
// the code that follows is a crude generator to create example guess data
//

// --- CONFIGURATION ---
$startDate = new DateTime('2026-04-01');
$endDate   = new DateTime('2026-04-30');
$peakDays  = ['2026-04-08', '2026-04-12', '2026-04-20'];
$totalHitsToGenerate = 1150; 

// GENERATE WEIGHTS FOR EACH DAY ---
$dailyWeights = [];
$period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->modify('+1 day'));

foreach ($period as $date) {
    $currentStr = $date->format('Y-m-d');
    $weight = 1; // Base weight

    foreach ($peakDays as $peak) {
        $peakDate = new DateTime($peak);
        $diff = $date->diff($peakDate)->days;

        if ($diff === 0) {
            $weight += 20; // The peak itself
        } elseif ($diff <= 3 && $date < $peakDate) {
            // "Leading up" bonus: closer to peak = higher weight
            $weight += (4 - $diff) * 5; 
        }
    }
    $dailyWeights[$currentStr] = $weight;
}

function getWeightedRandomDate($weights) {
    $totalWeight = array_sum($weights);
    $randomResult = mt_rand(1, $totalWeight);
    
    foreach ($weights as $date => $weight) {
        $randomResult -= $weight;
        if ($randomResult <= 0) return $date;
    }
}

//  GENERATE UNIQUE HITS ---
$results = [];
$seen = [];
// setting up random names
$firstNames = "Aarav|Abigail|Abner|Abram|Ace|Ada|Adalyn|Adam|Adan|Addilyn|Addison|Addisyn|Adelaide|Adelina|Adelyn|Adelynn|Aden|Adler|Adley|Adonis|Adrian|Adriana|Adriel|Ahmed|Aiden|Aileen|Ainsley|Aislinn|Alaina|Alaiya|Alana|Alani|Alanna|Alaric|Alayah|Albert|Alden|Aleah|Alejandra|Alejandro|Alena|Alessandra|Alexandra|Alexandria|Alexia|Alfred|Alfredo|Ali|Aliana|Alicia|Alina|Alivia|Aliyah|Allen|Allie|Allison|Allyson|Alma|Alonso|Alonzo|Alvaro|Alvin|Amaia|Amalia|Amara|Amari|Amaya|Amayah|Amber|Amina|Amirah|Amiyah|Amora|Ana|Anahi|Analia|Anastasia|Anaya|Anderson|Andre|Andres|Andrew|Andy|Angel|Angelina|Angie|Anika|Aniya|Aniyah|Anna|Annabelle|Annalise|Anne|Annie|Annika|Ansley|Anya|Apollo|Archie|Ari|Arian|Ariana|Ariel|Ariella|Arielle|Aries|Ariya|Arjun|Arlo|Armando|Aron|Artemis|Arturo|Aryan|Ashley|Ashlynn|Aspyn|Astrid|Athena|Atreus|Atticus|Aubrey|Aubrie|Aubrielle|Audrey|August|Augustine|Augustus|Aurelia|Austin|Avah|Avalynn|Avayah|Avery|Aya|Ayan|Aydin|Ayla|Ayleen|Aylin|Azariah|Aziel|Azrael|Bailee|Banks|Barbara|Baylor|Beckett|Beckham|Bella|Belle|Benjamin|Benson|Bentley|Bethany|Bianca|Bjorn|Blaine|Blair|Blaire|Blaise|Blake|Blakely|Bobby|Bode|Boden|Bodhi|Bodie|Bonnie|Boone|Bowen|Braden|Brady|Braelynn|Brandon|Branson|Braxton|Brayan|Brayden|Braylee|Braylon|Bria|Brian|Bridget|Briella|Brinley|Bristol|Brittany|Brixton|Brodie|Bronson|Brooke|Brooklynn|Bruce|Bruno|Bryan|Bryant|Bryce|Brycen|Brylee|Brynlee|Brynleigh|Brynn|Bryson|Byron|Cadence|Caiden|Cain|Cairo|Cal|Caleb|Cali|Callahan|Callan|Callen|Calliope|Camden|Cameron|Camila|Camilla|Camilo|Canaan|Capri|Cara|Carl|Carly|Carmelo|Carmen|Carolina|Caroline|Carter|Case|Casen|Casey|Cason|Cassandra|Cassidy|Castiel|Cataleya|Catalina|Cayden|Cayson|Cecilia|Cedric|Celia|Celine|Cesar|Chana|Chance|Chandler|Charleigh|Charli|Charlie|Chelsea|Cheyenne|Chloe|Christian|Christopher|Claire|Clare|Clark|Clay|Clementine|Cleo|Cody|Coen|Cohen|Colby|Cole|Colin|Collin|Collins|Colson|Colton|Connor|Conrad|Cooper|Cora|Coraline|Corbin|Corey|Cory|Crew|Crosby|Curtis|Cynthia|Cyrus|Dahlia|Daisy|Dakari|Dakota|Daleyza|Dallas|Dalton|Damari|Damian|Damien|Damir|Dangelo|Daniel|Daniela|Danielle|Danny|Dante|Daphne|Darian|Dariel|Dario|Darius|Darren|Darwin|Dash|Davian|Davina|Davion|Davis|Dawson|Dax|Deacon|Deandre|Delaney|Delilah|Denver|Derrick|Destiny|Devin|Devon|Dexter|Diana|Dillon|Dion|Dior|Dominic|Dominik|Donald|Donovan|Dorian|Dorothy|Douglas|Dream|Duke|Dulce|Duncan|Dustin|Dylan|Eddie|Eden|Edgar|Edith|Edward|Edwin|Egypt|Elaina|Eleanor|Elian|Eliana|Elianna|Eliel|Eliezer|Elijah|Elina|Elisa|Elisabeth|Elise|Eliseo|Elizabeth|Ella|Elle|Ellen|Elliana|Ellianna|Ellie|Elliot|Elliott|Ellis|Elodie|Eloise|Elsa|Elyse|Emani|Emanuel|Ember|Emberly|Emely|Emerald|Emerie|Emerson|Emiliano|Emilio|Emily|Emir|Emma|Emmaline|Emmalyn|Emmalynn|Emmanuel|Emory|Enrique|Enzo|Ephraim|Eric|Erick|Erik|Ernesto|Esme|Esmeralda|Esperanza|Estella|Esther|Estrella|Ethan|Eugene|Eva|Evan|Evangeline|Eve|Evelynn|Everest|Everett|Everlee|Everleigh|Everly|Evie|Ezekiel|Ezequiel|Ezra|Fabian|Faith|Fatima|Felicity|Felipe|Felix|Fernando|Finley|Finn|Finnegan|Finnley|Fiona|Fisher|Fletcher|Flora|Florence|Flynn|Ford|Forrest|Fox|Frances|Francesca|Franco|Frank|Frankie|Franklin|Frederick|Freyja|Frida|Gabriel|Gabriela|Gabrielle|Gael|Gage|Garrett|Gary|Gatlin|Gavin|Genesis|Genevieve|Georgia|Gerardo|Gia|Giana|Gianni|Giovanna|Giovanni|Giselle|Giuliana|Gloria|Grace|Gracelyn|Gracie|Grady|Graham|Graysen|Grayson|Greta|Griffin|Gunnar|Gwen|Hadley|Hailey|Hakeem|Halle|Hamza|Hanna|Harley|Harlow|Harmony|Harold|Harper|Harris|Harry|Harvey|Hassan|Haven|Hayley|Hazel|Heath|Heaven|Heidi|Helen|Helena|Hendrix|Holden|Holland|Holly|Hope|Hudson|Hugh|Hunter|Ibrahim|Ila|Imani|Indie|Irene|Iris|Isaac|Isabel|Isabela|Isaiah|Israel|Issac|Itzel|Ivanna|Ivy|Iyla|Izabella|Jabari|Jace|Jacqueline|Jad|Jada|Jadiel|Jaiden|Jair|Jake|Jakob|Jakobe|Jalen|Jamari|Jamie|Jamir|Jamison|Jane|Jared|Jase|Jasiah|Jasmine|Jason|Javier|Jaxon|Jaxson|Jaxtyn|Jaxx|Jaxxon|Jayce|Jayceon|Jayda|Jayden|Jaylee|Jayleen|Jaylin|Jayson|Jaziel|Jazmine|Jedidiah|Jeffery|Jeffrey|Jeremiah|Jeremias|Jericho|Jesse|Jessica|Jessie|Jesus|Jianna|Jillian|Jimena|Jocelyn|Joe|Joey|Johan|Johanna|John|Johnathan|Johnny|Jolie|Jon|Jones|Jordan|Jorge|Joseph|Joshua|Josie|Journey|Jovie|Joyce|Joziah|Judah|Jude|Judith|Judson|Julia|Julian|Juliana|Julien|Juliet|Juliette|Julio|Julissa|Julius|June|Junior|Juniper|Justice|Justin|Kade|Kaden|Kadence|Kai|Kaia|Kailani|Kairi|Kaisley|Kaison|Kaitlyn|Kaiya|Kalani|Kalel|Kali|Kaliyah|Kallie|Kamari|Kamden|Kamdyn|Kameron|Kamilah|Kamiyah|Kamryn|Kane|Kannon|Kareem|Karina|Karson|Karsyn|Karter|Kase|Kashton|Kason|Kassidy|Kataleya|Katalina|Kate|Katherine|Kathryn|Katie|Kayla|Kaylee|Kayleigh|Kaylie|Kaysen|Keanu|Keaton|Keenan|Kehlani|Kellan|Kellen|Kelsey|Kelvin|Kenneth|Kensley|Kenzie|Kenzo|Kevin|Keyla|Khalani|Khalid|Khalil|Khari|Kian|Kiana|Kiara|Killian|Kimberly|King|Kingsley|Kingston|Kinley|Kinslee|Kinsley|Knox|Kobe|Koda|Kohen|Kora|Korbin|Korbyn|Krew|Kristian|Kristopher|Kye|Kyla|Kylan|Kylee|Kyleigh|Kyler|Kylian|Kylo|Kyng|Kyree|Kyro|Kyson|Lacey|Laila|Lainey|Lana|Lance|Landen|Landon|Landyn|Laney|Langston|Lara|Larry|Laurel|Lauryn|Lawrence|Lawson|Layla|Layne|Leah|Leandro|Ledger|Legacy|Legend|Leia|Leif|Leighton|Leila|Leilani|Leilany|Lena|Lennon|Leon|Leona|Leonard|Leonardo|Leonidas|Leslie|Lexi|Lexie|Lia|Lilith|Lilliana|Lillie|Lily|Lilyana|Lina|Lincoln|Liv|Livia|Logan|Lola|Loretta|Louis|Lucca|Lucia|Luciano|Lucille|Luella|Luis|Luisa|Luka|Lukas|Luke|Lyanna|Lyla|Lylah|Lyra|Lyric|Maci|Macie|Mackenzie|Macy|Madden|Maddox|Madeline|Madelyn|Madelynn|Madilyn|Madison|Madisyn|Maeve|Magdalena|Maggie|Magnolia|Magnus|Maia|Maisie|Maison|Major|Makai|Makayla|Makenna|Makenzie|Malani|Malaya|Malayah|Maleah|Malia|Malik|Maliyah|Manuel|Mara|Marcel|Marceline|Marcellus|Marcos|Margaret|Margot|Maria|Mariah|Mariam|Mariana|Marianna|Marie|Marilyn|Marisol|Marlee|Marleigh|Marley|Marlowe|Marshall|Martha|Martin|Marvin|Mason|Mateo|Mathew|Mathias|Matias|Matteo|Matthew|Maurice|Maverick|Mavis|Max|Maximiliano|Maximo|Maximus|Maxine|Maya|Mckenna|Mckinley|Megan|Meilani|Mekhi|Melanie|Melany|Melissa|Melody|Melvin|Memphis|Mercy|Messiah|Mia|Michael|Michaela|Michelle|Mikaela|Mikayla|Mila|Milan|Milani|Milena|Miley|Miller|Millie|Milo|Mira|Miranda|Misael|Mohammad|Mohammed|Molly|Monica|Monroe|Mordechai|Morgan|Moshe|Muhammad|Musa|Mya|Myla|Mylah|Mylo|Myra|Nadia|Nash|Natalie|Nataly|Nathalia|Nathalie|Nathan|Nathaniel|Navy|Naya|Nayeli|Nehemiah|Nelson|Nia|Nico|Nicolas|Niko|Nikolas|Nina|Nixon|Noa|Noe|Noelle|Nola|Nolan|Noor|Norah|Nova|Nyla|Nylah|Oaklee|Oakley|Oaklyn|Ocean|Octavia|Odin|Olive|Oliver|Onyx|Opal|Orion|Oscar|Osiris|Otis|Owen|Pablo|Paige|Paisleigh|Paloma|Paola|Paris|Parker|Patrick|Paula|Paulina|Payton|Pearl|Pedro|Penny|Persephone|Peter|Peyton|Phillip|Phoebe|Phoenix|Piper|Poppy|Porter|Presley|Preston|Princess|Princeton|Promise|Quentin|Quinn|Raelyn|Raelynn|Raina|Randy|Raphael|Raquel|Raul|Raven|Ray|Raya|Raylan|Raymond|Rayna|Rayne|Reagan|Reed|Reese|Reginald|Reign|Reina|Remington|Remy|Rene|Rex|Reyna|Rhett|Rhys|Richard|Riggs|Riley|River|Roberto|Robin|Rocco|Rocky|Rodney|Rogelio|Roger|Rohan|Roland|Rome|Romina|Ronald|Ronan|Ronin|Rory|Rosa|Rosalee|Rosalia|Rosalie|Rowan|Rowen|Roy|Royal|Royalty|Royce|Ruben|Ruby|Ruth|Ryan|Ryder|Ryleigh|Sabrina|Sage|Saige|Saint|Salem|Salvador|Salvatore|Sam|Samantha|Samara|Samir|Samson|Samuel|Santino|Sara|Sarah|Sarai|Sariah|Sariyah|Sasha|Saul|Savannah|Sawyer|Saylor|Scarlet|Scarlette|Scott|Scout|Sean|Selene|Serena|Serenity|Seven|Sevyn|Shane|Shawn|Shay|Shepherd|Shiloh|Siena|Sienna|Sierra|Simon|Sky|Skylar|Skyler|Sloan|Sloane|Sofia|Solomon|Sonny|Sophia|Sophie|Soren|Stanley|Stefan|Stephen|Sterling|Steven|Stevie|Stormi|Sunny|Sutton|Sylvia|Sylvie|Talia|Talon|Tatum|Taylor|Teresa|Terrance|Terry|Tessa|Thaddeus|Thalia|Thatcher|Theo|Theodora|Theodore|Tiffany|Timothy|Tinsley|Titan|Tomas|Tommy|Tori|Trace|Treasure|Trent|Trevor|Trinity|Tripp|Tristan|Tristen|Troy|Tru|Truett|Tucker|Turner|Tyler|Tyson|Uriah|Valentin|Valeria|Van|Vance|Veda|Vera|Veronica|Victor|Victoria|Vienna|Vihaan|Vincent|Vincenzo|Violet|Violette|Viviana|Vivienne|Wade|Walker|Wallace|Walter|Watson|Waverly|Waylon|Wells|Wes|Wesley|Wesson|Westin|Westley|Whitley|Will|Willa|William|Willie|Willow|Winston|Winter|Wren|Wrenley|Wynter|Xander|Xavier|Ximena|Xiomara|Yahir|Yara|Yareli|Yehuda|Yosef|Yusuf|Zachary|Zaid|Zain|Zainab|Zaire|Zakai|Zander|Zaniyah|Zaria|Zariah|Zariyah|Zavier|Zayd|Zayn|Zayne|Zechariah|Zeke|Zendaya|Zhuri|Zoe|Zoie|Zola|Zora|Zoya|Zyaire|";
$lastNames = "Abbott|Acevedo|Acosta|Adams|Adkins|Aguilar|Aguirre|Ahmed|Alexander|Ali|Allen|Allison|Alvarado|Alvarez|Andersen|Anderson|Andrews|Anthony|Archer|Arellano|Arias|Armstrong|Arnold|Ashley|Atkins|Atkinson|Austin|Avalos|Avila|Ayala|Baker|Baldwin|Ball|Ballard|Banks|Barber|Barker|Barnes|Barnett|Barr|Barrera|Barron|Barry|Bartlett|Bass|Bates|Bautista|Baxter|Beasley|Beck|Becker|Beil|Bell|Bender|Benjamin|Bennett|Benson|Bentley|Berg|Berger|Bernard|Berry|Best|Bishop|Black|Blackburn|Blackwell|Blair|Blake|Blanchard|Blankenship|Bond|Bonilla|Booker|Boone|Booth|Bowen|Bowers|Bowman|Boyd|Boyer|Bradley|Bradshaw|Brady|Branch|Brandt|Bravo|Brennan|Bridges|Briggs|Brock|Brown|Browning|Bruce|Bryan|Bryant|Buchanan|Buck|Buckley|Burch|Burgess|Burke|Burnett|Burns|Burton|Bush|Butler|Byrd|Cabrera|Cain|Calderon|Caldwell|Calhoun|Campbell|Cannon|Cano|Cantrell|Cardenas|Carey|Carlson|Carrillo|Carroll|Carter|Case|Casey|Castaneda|Castillo|Castro|Cervantes|Chambers|Chan|Chandler|Chapman|Charles|Chase|Chen|Cherry|Choi|Christensen|Christian|Church|Cisneros|Clark|Clarke|Clay|Clayton|Clements|Cline|Cobb|Cochran|Coffey|Cohen|Cole|Coleman|Collins|Colon|Combs|Compton|Conley|Conner|Contreras|Cook|Cooper|Cordova|Corona|Correa|Cortes|Cortez|Costa|Cox|Craig|Crane|Crawford|Crosby|Cross|Cruz|Cummings|Cunningham|Curtis|Dalton|Daniel|Daniels|Daugherty|Davenport|David|Davidson|Davila|Davis|Dawson|Dean|Dejesus|Delacruz|Delarosa|Deleon|Delgado|Dennis|Diaz|Dickerson|Dickson|Dillon|Dixon|Dodson|Dominguez|Donaldson|Donovan|Douglas|Doyle|Drake|Duffy|Duke|Duncan|Dunlap|Dunn|Duran|Durham|Dyer|Eaton|Edwards|Elliott|Ellis|Ellison|English|Erickson|Esparza|Espinosa|Espinoza|Esquivel|Estes|Estrada|Evans|Everett|Farley|Farmer|Farrell|Faulkner|Felix|Ferguson|Fernandez|Fields|Figueroa|Finley|Fischer|Fisher|Fitzgerald|Fleming|Fletcher|Flores|Flowers|Flynn|Foley|Ford|Fowler|Fox|Francis|Franco|Franklin|Frazier|Frederick|Freeman|French|Friedman|Frost|Frye|Fuentes|Fuller|Gaines|Galindo|Gallagher|Gallegos|Galvan|Garner|Garrett|Garrison|Garza|Gentry|George|Gibbs|Gibson|Gilbert|Giles|Gill|Gilmore|Glass|Glenn|Golden|Gomez|Gonzales|Gonzalez|Good|Goodman|Goodwin|Gordon|Gould|Graham|Grant|Graves|Gray|Green|Greene|Greer|Griffith|Gross|Guerra|Guerrero|Guevara|Gutierrez|Guzman|Hail|Hale|Haley|Hall|Hamilton|Hammond|Hampton|Hancock|Hanna|Hansen|Hanson|Hardin|Harding|Hardy|Harmon|Harper|Harrell|Harris|Harrison|Harvey|Hawkins|Hayden|Hayes|Haynes|Hebert|Henderson|Hendricks|Hendrix|Hensley|Henson|Herman|Hernandez|Herrera|Herring|Hess|Hester|Hickman|Hicks|Higgins|Hill|Hines|Hinton|Ho|Hobbs|Hodge|Hodges|Hoffman|Hogan|Holland|Holloway|Holmes|Holt|Hood|Hoover|Hopkins|Horn|Horne|Horton|House|Houston|Howard|Howe|Howell|Huang|Hubbard|Huber|Hudson|Huerta|Huff|Huffman|Hughes|Hull|Humphrey|Hunt|Hurley|Hutchinson|Huynh|Ingram|Jackson|Jacobs|Jacobson|James|Jaramillo|Jarvis|Jefferson|Jennings|Jensen|Johns|Johnson|Johnston|Jones|Juarez|Kane|Kaur|Keith|Keller|Kelley|Kelly|Kemp|Kent|King|Kirk|Klein|Kline|Knapp|Knight|Knox|Koch|Kramer|Krueger|Lam|Lamb|Lambert|Landry|Lane|Lang|Lara|Larsen|Lawson|Le|Leach|Leal|Leblanc|Leon|Levy|Lewis|Li|Lim|Lin|Lindsey|Liu|Livingston|Lloyd|Logan|Long|Lopez|Love|Lowe|Lowery|Lozano|Lu|Lucas|Lucero|Lugo|Luna|Lynch|Lynn|Lyons|Macdonald|Macias|Mack|Madden|Maddox|Mahoney|Maldonado|Malone|Mann|Marin|Marks|Marquez|Marsh|Marshall|Martin|Martinez|Mason|Massey|Mata|Mathews|Mathis|Matthews|Maxwell|May|Mayer|Mayo|Mays|McBride|McCann|McCarthy|McCarty|McClain|McClure|McCormick|McDaniel|McDonald|McDowell|McFarland|McGee|McGuire|McIntosh|McIntyre|McKay|McKee|McKenzie|McLaughlin|McLean|McMahon|McMillan|McPherson|Meadows|Medina|Medrano|Mejia|Melendez|Melton|Mendoza|Mercado|Merritt|Meyer|Meyers|Meza|Michael|Middleton|Miles|Miller|Mills|Miranda|Mitchell|Molina|Monroe|Montes|Montgomery|Montoya|Moon|Moore|Mora|Morales|Morgan|Morris|Morrison|Morrow|Morse|Morton|Moses|Mosley|Moss|Moyer|Mueller|Mullen|Mullins|Munoz|Murillo|Murphy|Murray|Myers|Nash|Nava|Navarro|Neal|Nelson|Newman|Newton|Nguyen|Nichols|Nicholson|Nielsen|Nixon|Noble|Nolan|Norman|Norris|Norton|Novak|Nunez|O’brien|Ochoa|O’Connell|O’Connor|Odom|Oliver|Olsen|Olson|O’Neal|O’Neill|Orozco|Orr|Ortega|Ortiz|Osborne|Owen|Owens|Pace|Pacheco|Padilla|Page|Palacios|Palmer|Park|Parker|Parks|Parra|Parrish|Parsons|Patel|Patrick|Patterson|Paul|Payne|Pearson|Pennington|Peralta|Perez|Perkins|Perry|Person|Peters|Petersen|Peterson|Pham|Phan|Phelps|Phillips|Pierce|Pineda|Pittman|Pollard|Ponce|Poole|Pope|Porter|Portillo|Potter|Potts|Powers|Pratt|Preston|Price|Prince|Proctor|Pruitt|Pugh|Quinn|Quintana|Quintero|Ramirez|Ramsey|Randolph|Rangel|Rasmussen|Ray|Raymond|Reeves|Reilly|Reyes|Reyna|Reynolds|Rhodes|Rice|Rich|Richard|Richards|Richardson|Richmond|Riley|Rios|Rivers|Roach|Robbins|Roberson|Robertson|Robinson|Robles|Rocha|Rodgers|Rogers|Rojas|Rollins|Roman|Romero|Rosales|Rosario|Rose|Ross|Roth|Rowe|Rowland|Rubio|Ruiz|Rush|Russell|Russo|Ryan|Salas|Salgado|Salinas|Sampson|Sanchez|Sanders|Sanford|Santana|Santiago|Santos|Savage|Schaefer|Schmidt|Schneider|Schultz|Schwartz|Sellers|Serrano|Sexton|Shaffer|Shah|Shannon|Sharp|Shaw|Shelton|Shepard|Shepherd|Sheppard|Sherman|Shields|Short|Sierra|Silva|Simmons|Simon|Simpson|Sims|Singh|Singleton|Skinner|Sloan|Small|Snow|Solis|Solomon|Sosa|Soto|Spears|Spence|Spencer|Stafford|Stanley|Stark|Steele|Stein|Stephens|Stephenson|Stevens|Stevenson|Stewart|Stokes|Stone|Stout|Strickland|Stuart|Suarez|Sullivan|Summers|Sutton|Swanson|Sweeney|Tang|Tanner|Tapia|Tate|Taylor|Terry|Thomas|Thompson|Thornton|Todd|Torres|Tran|Travis|Trejo|Trujillo|Truong|Tucker|Turner|Tyler|Valdez|Valencia|Valentine|Valenzuela|Vance|Vang|Vasquez|Vaughan|Vaughn|Vazquez|Vega|Velasquez|Velez|Ventura|Villa|Villalobos|Villanueva|Villegas|Vincent|Vo|Vu|Wade|Wagner|Walker|Wall|Waller|Walls|Walsh|Walter|Walters|Walton|Wang|Ward|Warner|Warren|Waters|Watkins|Watson|Watts|Weaver|Webb|Weber|Webster|Welch|Wells|Wheeler|Whitaker|White|Whitehead|Whitney|Wiggins|Wiley|Wilkerson|Wilkins|Wilkinson|Williams|Williamson|Willis|Wilson|Winters|Wise|Wolf|Wolfe|Wong|Woodard|Woodward|Wright|Wu|Wyatt|Xiong|Yang|Yates|Yoder|York|Yu|Zamora|Zavala|Zhang|Zuniga|";
$firstArr = explode('|', $firstNames);
$lastArr  = explode('|', $lastNames);
$firstCount = count($firstArr) - 1;
$lastCount  = count($lastArr) - 1;

while (count($results) < $totalHitsToGenerate) {
    $chosenDate = getWeightedRandomDate($dailyWeights);
    $hour       = mt_rand(0, 23);
    $segment    = [0, 15, 30, 45][mt_rand(0, 3)];
    
    // Create a unique key to prevent duplicates
    $uniqueKey = "{$chosenDate}-{$hour}-{$segment}";
    
    if (!isset($seen[$uniqueKey])) {
        $seen[$uniqueKey] = true;
        $fName = $firstArr[rand(0, $firstCount)];
        $lName = $lastArr[rand(0, $lastCount)];
        $results[] = [
            'date'    => $chosenDate,
            'hour'    => $hour,
            'segment' => $segment,
            'guessDate' => sprintf("%s %02d:%02d", date("Y-m-d", strtotime($chosenDate)), $hour, $segment),
            'guessInfoPublic' => "$fName $lName"
        ];
    }
    
    // if we exceed possible combinations
    if (count($seen) >= (count($dailyWeights) * 24 * 4)) break;
}

// Sort by date/time for readability
usort($results, function($a, $b) {
    return strcmp($a['date'], $b['date']) ?: $a['hour'] <=> $b['hour'] ?: $a['segment'] <=> $b['segment'];
});

echo json_encode($results, JSON_PRETTY_PRINT);