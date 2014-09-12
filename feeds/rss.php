<?php

    //Add a content type header to ensure proper execution
    header('Content-Type: application/rss+xml');

    //Output the XML declaration
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>

<rss version="2.0">
    <channel>

        <title>My First Blog</title>
        <link>http://testvlad.com/internship_blog/</link>
        <description>This blog is awesome.</description>
        <language>en-us</language>

    </channel>
</rss>