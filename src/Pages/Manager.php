<div class="container wrap">
    <?php
    if (isset($_POST['form_name'])) {
        if (isset($submit)) {
            call_user_func($submit);
        } elseif (isset($alert)) {
            call_user_func($alert);
        }
    }
        global $alert_show;
    if (isset($alert_show)) {
        echo $alert_show;
    }
    ?>
    <h1 class="wp-heading-inline">
        <?php echo isset($page_title) ? $page_title : "Title"; ?>
    </h1><a href="http://localhost/wp-admin/post-new.php?post_type=page" class="page-title-action">Add New</a>
    <hr class="wp-header-end">
    <ul class="subsubsub">
        <?php
        if (isset($section_header)) {
            echo $section_header;
        } else {
            ?>
            <li>
                <a href="/" class="section1">Section1 <span class="count">(<span class="section1-count">0</span>)</span> |</a>
            </li>
            <li>
                <a href="/" class="section2">Section2 <span class="count">(<span class="section2-count">0</span>)</span> |</a>
            </li>
            <li>
                <a href="/" class="section3">Section3 <span class="count">(<span class="section3-count">0</span>)</span> |</a>
            </li>
            <?php
        }
        ?>
    </ul>
    <table class="wp-list-table widefat fixed striped table-view-list">
        <?php
        if (isset($section_content)) {
            echo $section_content;
        } else {
            ?>
            <thead><tr></tr></thead>
            <tbody>
                <tr>
                    <th scope="row">
                        Name
                    </th>
                    <td>
                        Field
                    </td>
                </tr>    
            </tbody>
            <tfoot></tfoot>
            <?php
        }
        ?>
    </table>
</div>
