<br />

<b>Enter a name you would like to register for your website (domain name):</b>
<form method="get" action="domain.php">
    <label><b>www.</b></label>
    <input type="text" name="domain">
    <select size="1" name="ext">
    <option selected value="com">.com</option>
    <option value="net">.net</option>
    <option value="org">.org</option>
    <option value="or.ke">.or.ke</option>
    <option value="info">.info</option>
    <option value="biz">.biz</option>
    <option value="co.ke">.co.ke</option>
    <option value="ke">.ke</option>
    <option value="travel">.travel</option>
    <option value="africa">.africa</option>
    <option value="academy">.academy</option>
    <option value="ac.ke">.ac.ke</option>
    <option value="sc.ke">.sc.ke</option>
    <option value="me.ke">.me.ke</option>
    <option value="mobi.ke">.mobi.ke</option>
    <option value="info.ke">.info.ke</option>
    </select>

    </select>
    <input type="hidden" name="option" value="check">
    <input type="hidden" name="src" value="dr">
    <?php if (isset($_POST['hosting']))
    { ?>
    <input type="hidden" name="hosting" value="<?php echo $_POST['hosting']; ?>">
   <?php }?>
    <input type="submit" value="Check">
    </form>

<?php
if (isset($_POST['hosting']))
{
?>
<p><b><a href="./reserve.php?h=<?php echo $_POST['hosting']; ?>&domain=example.com&src=dr">Already own a domain name?Skip the domain search</b></a></p>
<?php

} else {?>
<p> <b>Already own a domain name? <a href="./hosting.php?domain=example.com&src=dr">Add emails and hosting.</b></a></p>
<?php
}
?>