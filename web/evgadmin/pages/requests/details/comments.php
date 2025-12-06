<div class="req-comments-sec">
    <div class="cmnt-hed heading-6 mb20">
        Updates
    </div>
    <div class="req-comments">
        <div id="reqCommentsList" class="req-comments-list"></div>
        <div class="text-center pt25">
            <span class="pix-btn outlined sm" id="showMoreBtn">
                Show More
            </span>
            <div class="pix-spinner iblock md" id="reviewSpinner"></div>
            <div id="reviewError">
                <div class="mb10">
                    Oops. An error occurred with loading comments.
                </div>
                <span class="pix-btn danger sm outlined rounded" id="reviewErrorRetryBtn">
                    Retry
                </span>
            </div>
        </div>
        <div class="pt20 comment-form" id="mnCmntBox">
            <div class="mb10">
                <textarea cols="10" style="width: 100%;" rows="3" class="comment-inp" id="taskCommentInp" placeholder="Write Your Comment"></textarea>
            </div>
            <div>
                <span class="pix-btn md site post-comment-btn tp-comment" id="taskCommentPostBtn">
                    Post Comment
                </span>
            </div>
        </div>
    </div>
</div>