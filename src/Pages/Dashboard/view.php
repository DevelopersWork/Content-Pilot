<?php
    $results = $this -> view();
    $posts = $results['tbody'];
    $args = $results['args'];
?>


<h2 class="screen-reader-text">Filter posts list</h2>
<ul class="subsubsub">
    <li class="all"><a href="#action" class="current" aria-current="page">All <span class="count">(#count)</span></a> |
    </li>
    <!-- <li class="all"><a href="edit.php?post_type=post&amp;all_posts=1" class="current" aria-current="page">All <span class="count">(22)</span></a> |</li> -->
    <!-- <li class="mine"><a href="edit.php?post_type=post&amp;author=1">Mine <span class="count">(15)</span></a> |</li> -->
    <!-- <li class="publish"><a href="edit.php?post_status=publish&amp;post_type=post">Published <span class="count">(22)</span></a></li> -->
</ul>

<form method="POST" action="">

    <p class="search-box">
        <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
        <input type="search" id="post-search-input" name="s" value="" disabled>
        <input type="submit" id="search-submit" class="button" value="Search Posts" disabled>
    </p>

    <!-- <input type="hidden" id="_wpnonce" name="_wpnonce" value="0d71a93013"> -->
    <!-- <input type="hidden" name="_wp_http_referer" value="/wp-admin/edit.php"> -->

    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
            <select name="action" id="bulk-action-selector-top" disabled>
                <option value="-1">Bulk actions</option>
                <!-- <option value="edit" class="hide-if-no-js">Edit</option> -->
                <option value="trash">Move to Trash</option>
            </select>
            <input type="submit" id="doaction" class="button action" name="bulk_action_submit" value="Apply" disabled>
        </div>
        <div class="alignleft actions">
            <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
            <select name="m" id="filter-by-date" disabled>
                <option selected="selected" value="0">All dates</option>
                <!-- <option value="202202">February 2022</option>
                    <option value="202201">January 2022</option> 
                -->
            </select>
            <label class="screen-reader-text" for="cat">Filter by category</label>
            <select name="cat" id="cat" class="postform" disabled>
                <option value="0">All Categories</option>
                <!-- <option class="level-0" value="1">Uncategorized</option> -->
            </select>
            <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter" disabled>
        </div>

        <h2 class="screen-reader-text">Posts list navigation</h2>
        <div class="tablenav-pages"><span class="displaying-num"><?php echo $this -> store -> get('posts_per_page'); ?>
                items</span>
            <span class="pagination-links"><span class="tablenav-pages-navspan button disabled"
                    aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current
                        Page</label>
                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1"
                        aria-describedby="table-paging" disabled>
                    <!-- <span class="tablenav-paging-text"> of <span class="total-pages">1</span></span></span> -->
                    <a class="next-page button" href="#paged=2" disabled>
                        <span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span>
                    </a>
                    <a class="last-page button" href="#paged=2" disabled>
                        <span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span>
                    </a>
                </span>
        </div>
        <br class="clear">
    </div>
    <h2 class="screen-reader-text">Posts list</h2>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                    <input id="cb-select-all-1" type="checkbox" disabled>
                </td>
                <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                    <a href="#orderby=title&amp;order=asc">
                        <span>Title</span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
                <th scope="col" id="post_id" class="manage-column column-post_id">ID</th>
                <th scope="col" id="author" class="manage-column column-author">Author</th>
                <!-- <th scope="col" id="tags" class="manage-column column-tags">Service</th> -->
                <!-- <th scope="col" id="comments" class="manage-column column-comments num sortable desc">
            <a href="http://localhost/wp-admin/edit.php?orderby=comment_count&amp;order=asc">
                <span>
                    <span class="vers comment-grey-bubble" title="Comments">
                        <span class="screen-reader-text">Comments</span>
                    </span>
                </span>
                <span class="sorting-indicator"></span>
            </a>
        </th> -->
                <th scope="col" id="date" class="manage-column column-date sortable asc">
                    <a href="#orderby=date&amp;order=desc">
                        <span>Date</span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            </tr>
        </thead>

        <tbody id="the-list">
            <?php echo $posts;?>
        </tbody>

        <tfoot>
            <tr>
                <td class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                    <input id="cb-select-all-2" type="checkbox" disabled>
                </td>
                <th scope="col" class="manage-column column-title column-primary sortable desc">
                    <a href="#orderby=title&amp;order=asc">
                        <span>Title</span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
                <th scope="col" class="manage-column column-post_id">ID</th>
                <th scope="col" class="manage-column column-author">Author</th>
                <!-- <th scope="col" class="manage-column column-tags">Service</th> -->
                <!-- <th scope="col" class="manage-column column-comments num sortable desc">
            <a href="http://localhost/wp-admin/edit.php?orderby=comment_count&amp;order=asc">
                <span>
                    <span class="vers comment-grey-bubble" title="Comments">
                        <span class="screen-reader-text">Comments</span>
                    </span>
                </span>
                <span class="sorting-indicator"></span>
            </a>
        </th> -->
                <th scope="col" class="manage-column column-date sortable asc">
                    <a href="#orderby=date&amp;order=desc">
                        <span>Date</span>
                        <span class="sorting-indicator"></span>
                    </a>
                </th>
            </tr>
        </tfoot>

    </table>
</form>

<div id="ajax-response"></div>
<div class="clear"></div>
