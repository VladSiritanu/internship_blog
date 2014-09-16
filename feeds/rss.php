<?php

    // Include necessary files.
    include_once '../inc/function.inc.php';
    include_once '../inc/db.inc.php';

    // Open a database connection.
    $db =  new PDO(DB_INFO,DB_USER,DB_PASS);

    // Load all blog entries.
    $entries = retrieveEntries($db,'blog');

    // Remove the fulldisp flag.
    array_pop($entries);

    // Perform basic sanitization.
    $entries = sanitizeData($entries);

    // Add a content type header to ensure proper execution.
    header('Content-Type: application/rss+xml');

    // Output the XML declaration.
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>

<rss version="2.0">
  <channel>
    <title>My First Blog</title>
    <link>http://testvlad.com/internship_blog/</link>
    <description>This blog is awesome.</description>
    <language>en-us</language>
    <?php

      // Loop through the entries and generate RSS items.
      foreach ($entries as $entry) :

        // Escape HTML to avoid errors.
        $elem = htmlentities($entry['entry_text']);

        // Build the full URL to the entry.
        $url = 'http://testvlad.com/internship_blog/blog/' . $entry['url'];

        // Format the date correclty for RSS puDate.
        $date = date(DATE_RSS, strtotime($entry['entry_date']));

        ?>
        <item>
          <title><?php echo $entry['entry_title']; ?></title>
          <description> <?php echo $elem; ?></description>
          <link> <?php echo $url; ?> </link>
          <guid><?php echo $url; ?></guid>
          <pubDate><?php echo $date; ?></pubDate>
        </item>

      <?php
      endforeach;
    ?>
  </channel>
</rss>