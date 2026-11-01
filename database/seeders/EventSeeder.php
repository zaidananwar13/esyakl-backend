<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('event')->insert([
            'owner' => 5,
            'id_bahasa' => 1,
            'title' => 'Various You and Us Path to Learn Arabic with Expert Arabic Friends',
            'image' => '1.png',
            'desc' => '<p style="text-align:justify">Hello everyone!</p>

            <p style="text-align:justify"><br />
            Currently, Backend Development is becoming one of the most important stacks in building apps. Whether it&#39;s web apps, mobile apps, or desktop apps, all require a backend as a liaison between platforms.</p>
            
            <p style="text-align:justify">In this workshop, we will learn firsthand about the basic Rest API, what it is like and how to make it for beginners.</p>
            
            <p style="text-align:justify"><br />
            Let&#39;s join the collaboration workshop Google Developer Student Club Binus Malang with GITS Indonesia entitled &quot;Backend Fundamentals: Introduction to Rest API with AdonisJs&quot; which was presented by Hadian Rahmat - Chapter Lead Back End at GITS.id and moderator Ahmad Khoirudin - Core Team GDSC Binus Malang .</p>
            
            <p style="text-align:justify"><br />
            This workshop is held for FREE and Online on</p>
            
            <table align="left" border="0" cellpadding="0" cellspacing="0" style="width:500px">
                <tbody>
                    <tr>
                        <td style="text-align:justify">Date</td>
                        <td style="text-align:justify">:</td>
                        <td style="text-align:justify">Saturday, March 24, 2022</td>
                    </tr>
                    <tr>
                        <td style="text-align:justify">Time</td>
                        <td style="text-align:justify">:</td>
                        <td style="text-align:justify">16.00 - 18.00 WIB</td>
                    </tr>
                    <tr>
                        <td style="text-align:justify">Location</td>
                        <td style="text-align:justify">:</td>
                        <td style="text-align:justify">Zoom (Only for the first 300 zoom joins)</td>
                    </tr>
                </tbody>
            </table>
            
            <p style="text-align:justify">&nbsp;</p>
            
            <p style="text-align:justify">&nbsp;</p>
            
            <p style="text-align:justify">&nbsp;</p>
            
            <p style="text-align:justify">To take part in this workshop, it is hoped that you have installed node.js and postman!</p>
            
            <p style="text-align:justify">Let&#39;s join us devs! Learn and explore together! See you guys there.</p>
            ',
            'start' => strtotime(Carbon::parse("2022-10-17 00:00:00")->format('Y-m-d H:i:s')),
            'end' => strtotime(Carbon::parse("2022-11-25 00:00:00")->format('Y-m-d H:i:s')),
            'place' => 'Online',
            'location' => 'https://us02web.zoom.us/j/82968155912?pwd=MEpaNXhFbTh2c3ZWQ25iTysyVmo2Zz09#success',
            'quota' => 200,
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
    }
}
