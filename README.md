# PointShop-Donation
A simple donation processing system for Garry's Mod servers with PointShop. Users are automatically credited in PointShop Points when a payment is made, according to the POINTS_PER_DOLLAR value set by the server owner. Athough the points can be spent, all donations are logged permanently and implementing donator ranks on top of this system is easy (see the example in psdonation.lua). This software uses Stripe to process payements, which allows for a customizable checkout experience.

When users make payments, a running donation_total is kept on their account. Users who make numerous small payments will be credited identically to users who make one large payment of the same value. Also, if POINTS_PER_DOLLAR is increased, any user who donates again will be retroactively credited as if they donated their entire total_donation with the current POINTS_PER_DOLLAR setting.

##Super easy for users
![Sign in](http://i.imgur.com/UWeKj7f.png)

Sign in through Steam and see your own profile. No mucking about with Steam IDs!

---

![Choose amount](http://i.imgur.com/4hrjOTW.png)

Choose from 2 suggested amounts or enter your own amount to donate.

---

![Stripe](http://i.imgur.com/ov3xRNS.png)

Pay with Stripe, on the same webpage. No going off and needing to sign in on a third party site like PayPal. Be remembered when you come back - see your total cumulative donation.

---

Note that accepting donations does come with some risk. Malicious/fradulent users can create chargebacks which incurr fines against you. This typically happens with less than %1 of payments, but nonetheless __I am not responsible for any loss or damage which may come from using this software.__ I also recommend frequently backing up your MySQL database to protect against data loss.

---

##How to Install
Are you ready to make money off your Garry's Mod server?

* You need a web server with PHP and MySQL. I recommend XAMPP.

* Your web server should also be HTTPS secured, to protect users against some man-in-the-middle attacks.

* Put the contents of the 'php' folder into a directory named 'donate' in your web server's htdocs root.

* Fill out config.php. You need to sign up for [Stripe](https://dashboard.stripe.com/dashboard) to get your API keys and start accepting payments!
* Visit yoursite.com/donate once to create the data tables.

* On your Garry's Mod server, you need to install the  [tmysql4](http://blackawps-glua-modules.googlecode.com/svn/trunk/gm_tmysql4_boost/Release/) dll file to your garrysmod/lua/bin directory, and [libmysql](http://dev.mysql.com/downloads/connector/cpp/) to your base directory (alongside srcds.exe). For libmysql, download the 32 bit ZIP file, even if you are using a 64 bit OS.
* Place psdonation.lua in your pointshop addon's lua/pointshop/providers folder, configure psdonation.lua, and open pointshop's sh_config.lua to change the provider to "psdonation"
