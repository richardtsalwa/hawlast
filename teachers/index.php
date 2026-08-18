<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Matching</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #2596be;
            margin-bottom: 20px;
        }

        p {
            text-align: center;
            color: #777;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #49be25;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-bottom: 15px;
        }

        datalist option {
            /* Styling for datalist options can be added here */
        }

        button[type="submit"] {
            background-color: #be4d25;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .share-buttons {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .share-buttons a {
            margin: 0 10px;
            color: inherit;
        }

        .share-buttons i {
            font-size: 2em;
        }

        /* Media query for mobile responsiveness */
        @media only screen and (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }

            p {
                font-size: 14px;
            }

            button[type="submit"] {
                font-size: 16px;
            }

            .share-buttons i {
                font-size: 1.5em;
            }
        }
    </style>
        <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-P9KL6HJDLH"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-P9KL6HJDLH');
</script>
</head>
<body>
<div class="container">
    <div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v19.0&appId=202015096126" nonce="fuJg6fvS"></script>

    <h1>TSC Teacher Matching (Swap) Transfer</h1>
    <p>We  help you find a fellow teacher you can swap with for TSC Transfer!</p>
    <form action="match.php" method="post">
        
<label for="school">Do you teach in a primary/Secondary School:</label>
    <select name="school" required>
        <option value=""></option>
        <option value="primary">Primary</option>
        <option value="secondary">Secondary</option>
        </select><br>
        
        <!--
  <label for="school">Are you in primary/Secondary:</label>
    <select name="school" required>
        <option value=""></option>
        <option value="primary">Primary</option>
        <option value="secondary">Secondary</option>
        </select><br>
        -->
        
        <label for="county">Your Current County:</label>
       <input type="text" id="county" name="county" list="counties" placeholder="Where are you now?" required>
        <datalist id="counties">
            <option value="Mombasa">Mombasa</option>
            <option value="Kwale">Kwale</option>
            <option value="Kilifi">Kilifi</option>
            <option value="Tana River">Tana River</option>
            <option value="Lamu">Lamu</option>
            <option value="Taita/Taveta">Taita/Taveta</option>
            <option value="Garissa">Garissa</option>
            <option value="Wajir">Wajir</option>
            <option value="Mandera">Mandera</option>
            <option value="Marsabit">Marsabit</option>
            <option value="Isiolo">Isiolo</option>
            <option value="Meru">Meru</option>
            <option value="Tharaka-Nithi">Tharaka-Nithi</option>
            <option value="Embu">Embu</option>
            <option value="Kitui">Kitui</option>
            <option value="Machakos">Machakos</option>
            <option value="Makueni">Makueni</option>
            <option value="Nyandarua">Nyandarua</option>
            <option value="Nyeri">Nyeri</option>
            <option value="Kirinyaga">Kirinyaga</option>
            <option value="Murang'a">Murang'a</option>
            <option value="Kiambu">Kiambu</option>
            <option value="Turkana">Turkana</option>
            <option value="West Pokot">West Pokot</option>
            <option value="Samburu">Samburu</option>
            <option value="Trans Nzoia">Trans Nzoia</option>
            <option value="Uasin Gishu">Uasin Gishu</option>
            <option value="Elgeyo/Marakwet">Elgeyo/Marakwet</option>
            <option value="Nandi">Nandi</option>
            <option value="Baringo">Baringo</option>
            <option value="Laikipia">Laikipia</option>
            <option value="Nakuru">Nakuru</option>
            <option value="Narok">Narok</option>
            <option value="Kajiado">Kajiado</option>
            <option value="Kericho">Kericho</option>
            <option value="Bomet">Bomet</option>
            <option value="Kakamega">Kakamega</option>
            <option value="Vihiga">Vihiga</option>
            <option value="Bungoma">Bungoma</option>
            <option value="Busia">Busia</option>
            <option value="Siaya">Siaya</option>
            <option value="Kisumu">Kisumu</option>
            <option value="Homa Bay">Homa Bay</option>
            <option value="Migori">Migori</option>
            <option value="Kisii">Kisii</option>
            <option value="Nyamira">Nyamira</option>
            <option value="Nairobi City">Nairobi City</option>
        </datalist><br>
        
  <label for="subject1">Your Subject 1:</label>
  <input type="text" id="subject1" name="subject1" list="subjects" required>    
  <datalist id="subjects">
  <option value="Math">Math</option>
  <option value="English">English</option>
  <option value="Kiswahili">Kiswahili</option>
  <option value="Chemistry">Chemistry</option>
  <option value="Biology">Biology</option>
  <option value="Physics">Physics</option>
  <option value="Geography">Geography</option>
  <option value="CRE">CRE</option>
  <option value="IRE">IRE</option>
  <option value="History">History</option>
  <option value="Home Science">Home Science</option>
  <option value="German">German</option>
  <option value="Agriculture">Agriculture</option>
  <option value="Computer">Computer</option>
  <option value="Business Studies">Business Studies</option>
</datalist>
<br>

  <label for="subject2">Your Subject 2:</label>
  <input type="text" id="subject2" name="subject2" list="subjects" required>    
  <datalist id="subjects">
  <option value="Math">Math</option>
  <option value="English">English</option>
  <option value="Kiswahili">Kiswahili</option>
  <option value="Chemistry">Chemistry</option>
  <option value="Biology">Biology</option>
  <option value="Physics">Physics</option>
  <option value="Geography">Geography</option>
  <option value="CRE">CRE</option>
  <option value="IRE">IRE</option>
  <option value="History">History</option>
  <option value="Home Science">Home Science</option>
  <option value="German">German</option>
  <option value="Agriculture">Agriculture</option>
  <option value="Computer">Computer</option>
  <option value="Business Studies">Business Studies</option>
</datalist>
<br>

  <label for="subject3">Your Subject 3:</label>
  <input type="text" id="subject3" name="subject3" list="subjects">    
  <datalist id="subjects">
  <option value="Math">Math</option>
  <option value="English">English</option>
  <option value="Kiswahili">Kiswahili</option>
  <option value="Chemistry">Chemistry</option>
  <option value="Biology">Biology</option>
  <option value="Physics">Physics</option>
  <option value="Geography">Geography</option>
  <option value="CRE">CRE</option>
  <option value="IRE">IRE</option>
  <option value="History">History</option>
  <option value="Home Science">Home Science</option>
  <option value="German">German</option>
  <option value="Agriculture">Agriculture</option>
  <option value="Computer">Computer</option>
  <option value="Business Studies">Business Studies</option>
</datalist>
<br>

<label for="dream_county">Preferred County A:</label>
<input type="text" id="countya" name="countya" list="dreamcountiesa" required>
        <datalist id="dreamcountiesa">
            <option value="Mombasa">Mombasa</option>
            <option value="Kwale">Kwale</option>
            <option value="Kilifi">Kilifi</option>
            <option value="Tana River">Tana River</option>
            <option value="Lamu">Lamu</option>
            <option value="Taita/Taveta">Taita/Taveta</option>
            <option value="Garissa">Garissa</option>
            <option value="Wajir">Wajir</option>
            <option value="Mandera">Mandera</option>
            <option value="Marsabit">Marsabit</option>
            <option value="Isiolo">Isiolo</option>
            <option value="Meru">Meru</option>
            <option value="Tharaka-Nithi">Tharaka-Nithi</option>
            <option value="Embu">Embu</option>
            <option value="Kitui">Kitui</option>
            <option value="Machakos">Machakos</option>
            <option value="Makueni">Makueni</option>
            <option value="Nyandarua">Nyandarua</option>
            <option value="Nyeri">Nyeri</option>
            <option value="Kirinyaga">Kirinyaga</option>
            <option value="Murang'a">Murang'a</option>
            <option value="Kiambu">Kiambu</option>
            <option value="Turkana">Turkana</option>
            <option value="West Pokot">West Pokot</option>
            <option value="Samburu">Samburu</option>
            <option value="Trans Nzoia">Trans Nzoia</option>
            <option value="Uasin Gishu">Uasin Gishu</option>
            <option value="Elgeyo/Marakwet">Elgeyo/Marakwet</option>
            <option value="Nandi">Nandi</option>
            <option value="Baringo">Baringo</option>
            <option value="Laikipia">Laikipia</option>
            <option value="Nakuru">Nakuru</option>
            <option value="Narok">Narok</option>
            <option value="Kajiado">Kajiado</option>
            <option value="Kericho">Kericho</option>
            <option value="Bomet">Bomet</option>
            <option value="Kakamega">Kakamega</option>
            <option value="Vihiga">Vihiga</option>
            <option value="Bungoma">Bungoma</option>
            <option value="Busia">Busia</option>
            <option value="Siaya">Siaya</option>
            <option value="Kisumu">Kisumu</option>
            <option value="Homa Bay">Homa Bay</option>
            <option value="Migori">Migori</option>
            <option value="Kisii">Kisii</option>
            <option value="Nyamira">Nyamira</option>
            <option value="Nairobi City">Nairobi City</option>
        </datalist><br>
        
        <label for="dream_county">Preferred County B:</label>
<input type="text" id="countyb" name="countyb" list="dreamcountiesb">
        <datalist id="dreamcountiesb">
            <option value="Mombasa">Mombasa</option>
            <option value="Kwale">Kwale</option>
            <option value="Kilifi">Kilifi</option>
            <option value="Tana River">Tana River</option>
            <option value="Lamu">Lamu</option>
            <option value="Taita/Taveta">Taita/Taveta</option>
            <option value="Garissa">Garissa</option>
            <option value="Wajir">Wajir</option>
            <option value="Mandera">Mandera</option>
            <option value="Marsabit">Marsabit</option>
            <option value="Isiolo">Isiolo</option>
            <option value="Meru">Meru</option>
            <option value="Tharaka-Nithi">Tharaka-Nithi</option>
            <option value="Embu">Embu</option>
            <option value="Kitui">Kitui</option>
            <option value="Machakos">Machakos</option>
            <option value="Makueni">Makueni</option>
            <option value="Nyandarua">Nyandarua</option>
            <option value="Nyeri">Nyeri</option>
            <option value="Kirinyaga">Kirinyaga</option>
            <option value="Murang'a">Murang'a</option>
            <option value="Kiambu">Kiambu</option>
            <option value="Turkana">Turkana</option>
            <option value="West Pokot">West Pokot</option>
            <option value="Samburu">Samburu</option>
            <option value="Trans Nzoia">Trans Nzoia</option>
            <option value="Uasin Gishu">Uasin Gishu</option>
            <option value="Elgeyo/Marakwet">Elgeyo/Marakwet</option>
            <option value="Nandi">Nandi</option>
            <option value="Baringo">Baringo</option>
            <option value="Laikipia">Laikipia</option>
            <option value="Nakuru">Nakuru</option>
            <option value="Narok">Narok</option>
            <option value="Kajiado">Kajiado</option>
            <option value="Kericho">Kericho</option>
            <option value="Bomet">Bomet</option>
            <option value="Kakamega">Kakamega</option>
            <option value="Vihiga">Vihiga</option>
            <option value="Bungoma">Bungoma</option>
            <option value="Busia">Busia</option>
            <option value="Siaya">Siaya</option>
            <option value="Kisumu">Kisumu</option>
            <option value="Homa Bay">Homa Bay</option>
            <option value="Migori">Migori</option>
            <option value="Kisii">Kisii</option>
            <option value="Nyamira">Nyamira</option>
            <option value="Nairobi City">Nairobi City</option>
        </datalist><br>
        
<label for="dream_county">Preferred County C:</label>
<input type="text" id="countyc" name="countyc" list="dreamcountiesc">
        <datalist id="dreamcountiesc">
            <option value="Mombasa">Mombasa</option>
            <option value="Kwale">Kwale</option>
            <option value="Kilifi">Kilifi</option>
            <option value="Tana River">Tana River</option>
            <option value="Lamu">Lamu</option>
            <option value="Taita/Taveta">Taita/Taveta</option>
            <option value="Garissa">Garissa</option>
            <option value="Wajir">Wajir</option>
            <option value="Mandera">Mandera</option>
            <option value="Marsabit">Marsabit</option>
            <option value="Isiolo">Isiolo</option>
            <option value="Meru">Meru</option>
            <option value="Tharaka-Nithi">Tharaka-Nithi</option>
            <option value="Embu">Embu</option>
            <option value="Kitui">Kitui</option>
            <option value="Machakos">Machakos</option>
            <option value="Makueni">Makueni</option>
            <option value="Nyandarua">Nyandarua</option>
            <option value="Nyeri">Nyeri</option>
            <option value="Kirinyaga">Kirinyaga</option>
            <option value="Murang'a">Murang'a</option>
            <option value="Kiambu">Kiambu</option>
            <option value="Turkana">Turkana</option>
            <option value="West Pokot">West Pokot</option>
            <option value="Samburu">Samburu</option>
            <option value="Trans Nzoia">Trans Nzoia</option>
            <option value="Uasin Gishu">Uasin Gishu</option>
            <option value="Elgeyo/Marakwet">Elgeyo/Marakwet</option>
            <option value="Nandi">Nandi</option>
            <option value="Baringo">Baringo</option>
            <option value="Laikipia">Laikipia</option>
            <option value="Nakuru">Nakuru</option>
            <option value="Narok">Narok</option>
            <option value="Kajiado">Kajiado</option>
            <option value="Kericho">Kericho</option>
            <option value="Bomet">Bomet</option>
            <option value="Kakamega">Kakamega</option>
            <option value="Vihiga">Vihiga</option>
            <option value="Bungoma">Bungoma</option>
            <option value="Busia">Busia</option>
            <option value="Siaya">Siaya</option>
            <option value="Kisumu">Kisumu</option>
            <option value="Homa Bay">Homa Bay</option>
            <option value="Migori">Migori</option>
            <option value="Kisii">Kisii</option>
            <option value="Nyamira">Nyamira</option>
            <option value="Nairobi City">Nairobi City</option>
        </datalist><br>

<button type="submit">Find Match</button>
    </form>
    <br>
    <br>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="share-buttons">
  <a href="https://wa.me//send?text=TSC Teacher County to County Swap https://hawlast.com/teachers" title="Share on WhatsApp">
    <i class="fab fa-whatsapp fa-2x"></i>
  </a>
  <a href="https://twitter.com/share?url=[https://hawlast.com/teachers]&text=TSC Teacher to Teacher Swap -  County to County Swap " target="_blank" title="Share on Twitter">
    <i class="fab fa-twitter fa-2x"></i>
  </a>

<div class="fb-share-button" data-href="https://www.hawlast.com/teachers/" data-layout="" data-size=""><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fwww.hawlast.com%2Fteachers%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>

</div>
</body>
</html>