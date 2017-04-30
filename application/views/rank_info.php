<style type="text/css">
    .kkd-table font{color:red;font-size:12px;}
    .rank{float:left;padding:5px 10px;margin-right:10px;}
    .rank.order {
        background: url('/images/common/icon-order.png') no-repeat  0 center;
        padding:5px 10px 5px 30px;
        margin-right:0px;
    }
    .rank.type{
        padding:5px 15px;
    }
    .rank.type:hover,.rank.active{
        -moz-box-shadow: 0px 5px 15px rgba(0, 120, 255,0.4);
        -webkit-box-shadow: 0px 5px 15px rgba(0, 120, 255,0.4);
        box-shadow: 0px 5px 15px rgba(0, 120, 255,0.4);
        -moz-border-radius: 15px;
        -webkit-border-radius: 15px;
        border-radius: 15px;
        background-color:#0078ff;
        color:#efefef;
    }
</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title"><?php echo $teacher_name; ?>的考核项目明细</div>
        <div class="main-header">
            <span class="rank order" href="javascript:void(0);">排序：</span>
            <a class="rank type active" href="javascript:rank_order(0);" data-rank="0">专业标准</a>
            <a class="rank type" href="javascript:rank_order(1);" data-rank="1">素养标准</a>
            <a class="rank type" href="javascript:rank_order(2);" data-rank="2">学术标准</a>
        </div>
        <div class="main-content">
            <div class="main-content-warp">
                <table class="kkd-table table-hover">
                    <thead>
                    <tr>
                        <th>项目</th>
                        <th>分值</th>
                        <th>内容</th>
                        <th>提交日期</th>
                        <th>核准人</th>
                    </tr>
                    </thead>
                    <tbody id="kkd-data-target">
                    <tr>
                        <td colspan="5"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pages clearfix">
            <ul class="pagination" id="kkd-pagination">
                <li><span class="previous">&nbsp;</span></li>
                <li class="active"><span>1</span></li>
                <li><span class="next">&nbsp;</span></li>
            </ul>
        </div>
    </div>
</div>
<script type="text/template" id="template-assessment-data">
    <tr>
        <td class="txt" title="[assessment_name]">[assessment_name]</td>
        <td>[item_number]</td>
        <td class="txt" title="[item_title]">[item_title]</td>
        <td>[commit_datetime]</td>
        <td>[auditor_name]</td>
    </tr>
</script>
<script type="text/javascript">
    var request_page = '/rank/';
    var select_assessment_type = 0;
    var teacher_id = <?php echo $teacher_id; ?>;
    function kkd_init(){
        if(isNaN(teacher_id) || teacher_id == 0){
            $("#kkd-data-target").html('<tr><td colspan="5">'+ kkd_nonedata_txt +'</td></tr>');
            return alert(KKD_MESSAGE_ERROR_PARAMETER);
        }
        request_page = request_page+teacher_id;
        kkd_data_init(request_page+'?assessment_type=0');
    }

    function kkd_data_init(url){
        $.ajax({
            url: url,
            dataType:'json',
            type:'get',
            beforeSend:function(){
                $("#kkd-data-target").html('<tr><td colspan="5">'+ kkd_loading_txt +'</td></tr>');
            },
            success: load_success
        });
    }
    function load_success(result)
    {
        if(result.code == 200 ){
            var temp = $("#template-assessment-data").html();
            var temp_data = [];
            if(result.data.data.length == 0) {
                $("#kkd-data-target").html('<tr><td colspan="5">'+ kkd_nonedata_txt +'</td></tr>');
            }else{
                $(result.data.data).each(function(i,o){
                    var ls = temp.replace(/\[assessment_name\]/g, o.assessment_name).replace('[item_number]', o.item_number).replace(/\[item_title\]/g, o.item_title)
                        .replace('[commit_datetime]', o.commit_datetime).replace('[auditor_name]', o.auditor_name);
                    temp_data.push(ls);
                });
                $("#kkd-data-target").html(temp_data.join(''));
            }
            pages_init(result.data.total,result.data.current_page,result.data.total_page);
        }
        else {
            alert(result.info);
        }
    }
    function rank_order(i)
    {
        select_assessment_type = i;
        $(".type").removeClass('active');
        $(".type").eq(i).addClass('active');
        kkd_data_init(request_page+'?assessment_type='+i);
    }
    function location_url(p)
    {
        kkd_data_init(request_page+'?assessment_type='+select_assessment_type+'&page='+p);
    }
</script>