<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blog')->insert([
            'owner' => 9,
            'id_bahasa' => 1,
            'image' => 'default.jpg',
            'title' => '7 things you must understand before learning Arabic',
            'content' => '<p><strong>Step 01 - Let&#39;s get started</strong><br />
            But I must explain to you how all this mistaken idea of denouncing plea and we praising pain was born and I will give you a complete account of the system, and expound the actual teac of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes.<br />
            Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces no resultant pleasure?&quot;<br />
            &ldquo;But I must explain to you how all this mistaken idea of denouncing plea and praising pain.&rdquo;<br />
            <strong>Step 02 - Ask for feedback</strong><br />
            But I must explain to you how all this mistaken idea of denouncing plea and we praising pain was born and I will give you a complete account of the system, and expound the actual teac of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes.<br />
            Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise.</p>',
            'category' => 'Category A',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
        
        
        DB::table('blog')->insert([
            'owner' => 6,
            'id_bahasa' => 1,
            'image' => '2.jpg',
            'title' => 'List of things you must know before learning Arabic',
            'content' => '<p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">If you have a desire to learn a language other than your mother tongue, you can choose Arabic as one of the languages you can learn. But before you start learning it, it&#39;s a good idea to know a few facts about the Arabic language. Quoting from Cactus Language, here are some interesting facts about Arabic that you can know. </span></span></p>

            <ol>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic is a Semitic language</span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic is a Semitic language which means it is spoken by more than 200 million people. Arabic itself comes from the Arabian Peninsula. When the Muslim world developed, Arabic then spread to North Africa and West Asia.&nbsp; </span></span></p>
            
            <ol start="2">
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">The fifth most spoken language in the world</span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic is officially used in 26 countries, including Algeria, Egypt, Israel (along with Hebrew), Lebanon, Morocco, Saudi Arabia, Tunisia, and the UAE. This makes it the fifth most spoken language in the world </span></span></p>
            
            <ol start="3">
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic has influenced many languages</span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic has influenced a large number of languages, including most European languages as well as Asian and African languages. English has many Arabic loanwords such as alcohol, algebra, algorithm, mean, candy, caravan, cotton, giraffe, guitar, jar, lemon, lilac, magazine, sugar, and talisman. </span></span></p>
            
            <ol start="4">
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">There are about 14 words that describe &ldquo;love&rdquo; </span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Each Arabic word that is used for &quot;love&quot; describes a certain stage of falling in love. For example, &quot;hawa&quot; describes the initial attraction to someone, &quot;huyum&quot; the loss of mind that follows, and &quot;ishq&quot; blind desire. </span></span></p>
            
            <ol start="5">
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Has the same characteristics as Hebrew</span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic and Hebrew are both Semitic languages. The similarities between the two languages make it easier for Arabic speakers to learn Hebrew and vice versa. </span></span></p>
            
            <ol start="6">
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic has no capital letters </span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Maybe there are still many people who still do not know that in fact, Arabic does not have capital letters. That&#39;s because the Arabic alphabet uses quotation marks for emphasis, not capital letters. </span></span></p>
            
            <ol start="7">
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic has sounds that are not available in other languages</span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Some non-native speakers find it difficult to pronounce words in Arabic, due to some sounds that are not common in other languages that are widely spoken. For example, &#39;?&#39; is the &#39;h&#39; sound found only in Arabic. </span></span></p>
            
            <ol start="8">
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic words are written from right to left</span></span></li>
            </ol>
            
            <p style="margin-left:48px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Lastly, the way Arabic is written is different from other languages because it is written from right to left, not from left to right. Unlike many other widely spoken languages, Arabic words are written from right to left, and Arabic numbers are written from left to right.</span></span></p>
            ',
            'category' => 'Category A',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
        
        DB::table('blog')->insert([
            'owner' => 6,
            'id_bahasa' => 1,
            'image' => '3.jpg',
            'title' => 'Why should you learn Arabic?',
            'content' => '<p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">With more than 300 million speakers worldwide, Arabic is the fifth most widely spoken language and one of the most widely spoken languages worldwide.</span></span></p>

            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arabic is a language that deserves a lot of attention because of its rich geographical, historical, philosophical, economic, cultural, scientific, geopolitical, and strategic sides.</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">There are two main types of Arabic: dialects of Arabic (colloquial Arabic), commonly referred to as Modern Standard Arabic which is used in speaking, and literary Arabic (standard Arabic), the older type of Arabic used in the Bible. The holy religion of Islam is the Quran.</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Just like learning to play a musical instrument, learning the Arabic alphabet, Arabic vocabulary, Arabic grammar, and written Arabic will stimulate your mind and help it stay active. But the benefits of learning Arabic aren&#39;t just that, there are many more benefits that you can get from learning Arabic. Therefore, this article will contain some of the benefits of learning Arabic.</span></span></p>
            
            <ul>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">From the point of view of Islam.</span></span>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">In the midst of an increasingly global movement, the ability to speak a foreign language is a skill that everyone must possess to survive and succeed in this era. So what foreign languages are good to learn? Arabic is one of the great languages in the world. Why must Arabic? If you are looking for what Arabic can provide for you, whether for your personal or professional needs, how important it is to learn Arabic, we are here to help you provide the best answers. Even though we don&#39;t live in an Arabic-speaking country, shouldn&#39;t we as Muslims learn Arabic, because it is the language that is used in the Quran?</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Here are some reasons why Arabic is important.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">The first is that Arabic is the language that is used in the Al-Quran Al-Karim. This fact should be the strongest reason why you should learn Arabic. There are more than ten verses of the Qur&#39;an that have mentioned the features of the Arabic language, for example:</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">وَلَقَدْ ضَرَبْنَا لِلنَّاسِ فِي هَذَا الْقُرْآنِ مِنْ كُلِّ مَثَلٍ لَعَلَّهُمْ يَتَذَكَّرُونَ . قُرْآنًا عَرَبِيًّا غَيْرَ ذِي عِوَجٍ لَعَلَّهُمْ يَتَّقُونَ</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">&ldquo;In this Quran, We give people all kinds of examples, so that they pay attention. A Quran in Arabic, free from defects, so that they may become righteous.&rdquo; (QS. Az-Zumar: 27-28).</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">In addition, through understanding the Arabic language, it is easy for us to memorize, understand, teach, and also apply the teachings in the Qur&rsquo;an to each of our lives. Moreover, it is easy for you to understand Islam as a whole, seek knowledge directly from great scholars, and of course Arabic is the most noble language.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">In addition to the need for religious inspiration that we need with Arabic, getting used to using Arabic will affect our point of view to see life better. With the provision of Arabic, you can also maintain your words and behavior every day. What needs to be agreed is that there is nothing bad to be gained by learning Arabic. Arabic is the most concise language but the broadest in meaning.</span></span></p>
                </li>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arab has brought many valuable influences into the world culture.</span></span></li>
                <li>
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">The discovery of Arab culture in a number of fields has inspired many Western nations. Literature, poetry, calligraphy, mathematics, astronomy, medicine, and philosophy all have their roots in Arab culture.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">The influence of the Arabic language is truly extraordinary. Arab civilization is famous in the world of art, literature, and science. Studying Modern Standard Arabic allows you to fully appreciate an extraordinary heritage.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">In addition, if you are interested in studying religion, especially Islam, studying the Qur&rsquo;an by learning Arabic will be very helpful.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Since the 7th century, when Arabs began to explore Europe and establish settlements in Spain and southern France, Arabic words continued to be adopted into many southern European languages, eventually being widely adopted into English.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Words like alchemy, algorithm, algebra, caramel, elixir, orange, zero, apricot, gazelle, sorbet, alcohol, magazine, coffee, sugar&mdash;and many others&mdash;were inspired by both spoken and written Arabic.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Throughout history, the influence of Arab civilization in the fields of literature and poetry offered another reason to study Arabic. Persian, Arabic, and Maghreb writers of the past have left us timeless works so that you can learn Arabic words and idiomatic expressions better. Reading Arabic texts and understanding these idioms outside of class are excellent exercises to help you learn Arabic.</span></span></p>
                </li>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">From an educational point of view, learning Arabic can provide a number of advantages.</span></span></li>
                <li>
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">First, Arabic-speaking students will have a number of professional career opportunities as the economies of Arab countries are growing rapidly in the Middle East and North Africa.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Second, being able to speak Arabic allows you to communicate with more than 300 million native Arabic speakers. Therefore, learning Arabic can provide excellent international career opportunities.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Being able to speak Arabic also gives you a big advantage in business in Arabic-speaking countries. Given the complexity of Arabic and its grammatical structure, if you can make yourself understood, any native Arabic speaker you speak to will be greatly impressed by your efforts to communicate in their language rather than relying on English.</span></span></p>
            
                <p style="margin-left:24px"><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">You can also become a journalist, international lawyer, humanitarian worker, and others. In addition, you will find that your Arabic classes in high school as well as in college are very helpful.</span></span></p>
            
                <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Maybe it has never crossed your mind before to learn Arabic. However, after reading this article, we are sure that you no longer have any reason not to study it. This language offers lucrative career opportunities, and you have nothing to lose by learning this language. On the other hand, you will broaden your horizons because this language is spoken by more than 200 million people in the world. So what are you waiting for? Start learning Arabic on Arabic-Go from now on!</span></span></p>
                </li>
            </ul>
            ',
            'category' => 'Category A',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
        
        DB::table('blog')->insert([
            'owner' => 6,
            'id_bahasa' => 1,
            'image' => '4.jpg',
            'title' => '4 Things You Need to Pay Attention to Before Learning Arabic',
            'content' => '<p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Do you want to start learning Arabic? That&#39;s a good thing, with over 300 million speakers worldwide, Arabic is the fifth most spoken language and one of the most widely spoken languages worldwide. In addition, being fluent in Arabic also opens up other good opportunities in various fields. But before you start learning Arabic, here are 4 things you should consider.</span></span></p>

            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">First:</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Determine your intent and purpose for learning Arabic. Want to be good at classical Islamic literature? Want to just be able to read? Want to improve grammar? Want modern Arabic, for example, just want to enjoy Arabic newspapers or movies? Or just want to be able to have common every day Arabic conversations? Each different intent has different risks.</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Try to determine what you want first. This choice is important not only to decide your next stage but also to consider your daily busyness and work. Don&#39;t let learning Arabic distract you from your main focus. Because after all, for some people, learning Arabic is not mandatory.</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Second:</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">If you&#39;re a Muslim and already able to read and write Arabic, it is very good to be proficient in reading the Qur&#39;an. The Qur&#39;an should be the biggest motivation in learning Arabic. This is where the &quot;worship&quot; of learning Arabic lies.</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">If you are not able to read the Qur&#39;an, it is highly recommended to learn Arabic by learning to read the Qur&#39;an first. Why? Because that&#39;s where we are led to pronounce Arabic letters perfectly, because that&#39;s where the intricacies of the language that is used for the Qur&#39;an exist, including when we learn Arabic style.</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Inshaallah, if you learn to read the Qur&#39;an for two hours a day, in three months you will be able to read it fluently.</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Third:</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Choose a trusted institution. Since you&#39;re new to the world of Arabic, you have to ask an expert and consider many aspects. The institute covers aspects of teaching staff, methodology, reference books, time, discipline, experience to course fees. In addition, each institution also has its own focus and advantages. </span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Fourth:</span></span></p>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Find friends that can help you learn Arabic. Friends are often an important motivation in doing something and language is no exception. In fact, in learning a language, friends occupy a strategic position, because you can ask them to practice Arabic together, you guys will not be ashamed to ask each other questions, and most importantly you can remind each other.</span></span></p>
            ',
            'category' => 'Category A',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]);
        
        DB::table('blog')->insert([
            'owner' => 6,
            'id_bahasa' => 1,
            'image' => '5.jpg',
            'title' => 'The cultural heritage of Arabic-speaking countries',
            'content' => '<p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Arab countries are one of the countries that has a large contribution to knowledge in ancient times. When the Christian clergies were still living in solitary confinement in libraries and the Western world was still busy denouncing anything immodest, Andalusian and Maghreb Muslims had attempted to create a &ldquo;golden age&rdquo; that allowed them to make spectacular progress, which they later brought with them to Europe during the medieval period we know as the &ldquo;Dark Ages&rdquo;. Some examples of Arab civilization heritage include:</span></span></p>

            <ul>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Harun ar-Rashid (763&ndash;809), the fifth caliph of the Abbasid dynasty founded the first hospital in the 9th century.</span></span></li>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Ibnu Nafis (1213-1288), was a Syrian scientist who discovered pulmonary circulation and whose writings on the subject were used in the West until the 16th century.</span></span></li>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Nasir ad-Din al-Tusi (1201-1274), was an Iranian scientist who developed the most advanced diagram of the solar system of his time. It was his work that inspired Nicolaus Copernicus (1473-1543) 250 years later.</span></span></li>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">The astronomical instruments (astrolabes, astronomical clocks, sundials, compasses) used by European colonizers to reach the Americas were designed and perfected by Arab civilizations.</span></span></li>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">Averroes or Ibnu Sina (1126&ndash;1198), was a famous philosopher, lawyer, and doctor. He had a great influence on medieval intellectualism. His writings contributed to the understanding of Aristotle&#39;s great works, and he became the founder of secular thought in Western Europe.</span></span></li>
                <li><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">In the architecture field, Arabians left a very diverse heritage and many of which can still be seen and visited both in Arabic-speaking countries and in other countries such as Spain, France, the Balkans, and others.</span></span></li>
            </ul>
            
            <p><span style="font-size:12pt"><span style="font-family:Calibri,sans-serif">The things that have been mentioned above are just some of the contributions of Muslim countries to world civilization. There are many other contributions that may not be mentioned here and are not even recorded by history. But hopefully with the information that has been provided here can prove how big the influence of Islamic countries to world civilization.</span></span></p>
            ',
            'category' => 'Category A',
            'created_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s')),
            'updated_at' => strtotime(Carbon::now()->format('Y-m-d H:i:s'))
        ]); 
    }
}
