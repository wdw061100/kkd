<style type="text/css">

</style>
<div class="main">
    <div class="main-warp">
        <div class="main-title">排行榜<font style="font-size: 14px;color:#dedede;" id="count_person"></font></div>
        <div class="main-header">
            <span class="rank order" href="javascript:void(0);">排序：</span>
            <a class="rank type active" href="javascript:rank_order(0);" data-rank="0">专业标准</a>
            <a class="rank type" href="javascript:rank_order(1);" data-rank="1">素养标准</a>
            <a class="rank type" href="javascript:rank_order(2);" data-rank="2">学术标准</a>
            <div class="search-warp">
                <div class="select-box">
                    <select class="cs-select kkd-skin" id="s_teacher_grade">
                        <option value="">所有年级</option>
                        <option value="1">一年级</option>
                        <option value="2">二年级</option>
                        <option value="3">三年级</option>
                        <option value="4">四年级</option>
                        <option value="5">五年级</option>
                        <option value="6">六年级</option>
                    </select>
                </div>
                <div class="select-box">
                    <select class="cs-select kkd-skin" id="s_teacher_subject">
                        <option value="" disabled selected>所有学科</option>
                    </select>
                </div>
                <div class="select-box">
                    <input type="text" placeholder="请输入您要查找的内容" id="s_keywords" class="form-control search" />
                </div>
                <div class="select-box right">
                    <button class="btn btn-search" data-lock="false" onclick="search()" type="button">搜索</button>
                </div>
            </div>
        </div>
        <div class="main-content">
            <div class="main-content-warp">
                <div style="text-align: right;margin-bottom:10px;"><a style="font-size:12px;font-weight: normal;" href="/Home/save_excel" target="_blank">保存到Excel</a></div>
                <table class="kkd-table table-hover">
                    <thead>
                    <tr>
                        <th>排名</th>
                        <th>教师<font> ( 点击查看详情 )</font></th>
                        <th>学科</th>
                        <th>年级</th>
                        <th>专业</th>
                        <th>素养</th>
                        <th>学术</th>
                    </tr>
                    </thead>
                    <tbody id="kkd-data-target">
                    <tr>
                        <td colspan="7"></td>
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
        <th>[rank_number]</th>
        <td><a href="/Home/rank_info/[teacher_id]?user=[teacher_name]" target="_blank">[teacher_name]</a></td>
        <td class="txt" title="[assessment_name]">[teacher_subject]</td>
        <td class="txt" title="[item_title]">[grade_number]</td>
        <td>[sum_type0]</td>
        <td>[sum_type1]</td>
        <td>[sum_type2]</td>
    </tr>
</script>
<script type="text/javascript">
    var kkd_school_config = <?php echo $KKD_SCHOOL_CONFIG?>;
    var select_assessment_type = 0;
    function search(pars)
    {
        var teacher_grade = $("#s_teacher_grade").val();
        var teacher_subject = $("#s_teacher_subject").val();
        var keywords = $.trim($("#s_keywords").val());

        teacher_grade = (teacher_grade == null)?'':teacher_grade;
        teacher_subject = (teacher_subject == null)?'':teacher_subject;
        keywords = (keywords == null)?'':keywords;
        var s_data = [];
        s_data.push('grade_number='+teacher_grade);
        s_data.push('teacher_subject='+teacher_subject);
        s_data.push('keywords='+keywords);
        s_data.push('assessment_type='+select_assessment_type);

        if(pars === 1) return s_data.join('&');
        else location_url(s_data.join('&'));
    }

    function rank_order(i)
    {
        select_assessment_type = i;
        $(".type").removeClass('active');
        $(".type").eq(i).addClass('active');
        search();
    }

    function kkd_init(){
        kkd_teacher_subject_inti('#s_teacher_subject');
        kkd_select_int();
        kkd_data_init();
    }

    function kkd_teacher_subject_inti(obj)
    {
        var ss =kkd_school_config['school_subject'];
        var vas = ss.split(',');
        var temp_data = [];
        temp_data.push('<option value="">所有学科</option>');
        var temp_model = '<option value="[value]">[value]</option>';
        for(var i =0 ;i<vas.length;i++)
        {
            temp_data.push(temp_model.replace(/\[value\]/g,vas[i]));
        }
        $(obj).html(temp_data.join(''));
    }

    function kkd_data_init(url){
        url = (url)?url:'/rank';
        $.ajax({
            url: url,
            dataType:'json',
            type:'get',
            beforeSend:function(){
                $("#kkd-data-target").html('<tr><td colspan="7">'+ kkd_loading_txt +'</td></tr>');
            },
            success: load_success,
            error:kkd_ajax_error
        });
    }
    function load_success(result)
    {
        if(result.code == 200 ){
            var temp = $("#template-assessment-data").html();
            var temp_data = [];
            if(result.data.data.length == 0) {
                $("#kkd-data-target").html('<tr><td colspan="7">'+ kkd_nonedata_txt +'</td></tr>');
            }else{
                $(result.data.data).each(function(i,o){
                    var ls = temp.replace('[rank_number]', o.rank_number).replace('[teacher_id]', o.teacher_id).replace(/\[teacher_name\]/g, o.teacher_name)
                        .replace('[teacher_subject]', o.teacher_subject).replace('[grade_number]', kkd_class_values[o.grade_number]+'年级')
                        .replace('[sum_type0]', o.sum_type0).replace('[sum_type1]', o.sum_type1).replace('[sum_type2]', o.sum_type2);
                    temp_data.push(ls);
                });
                $("#kkd-data-target").html(temp_data.join(''));
            }
            $("#count_person").text(' ( 总人数：'+result.data.total+' )');
            pages_init(result.data.total,result.data.current_page,result.data.total_page);
        }
        else {
            alert(result.info);
        }
    }

    function location_url(p)
    {
        if(typeof p === "number"){
            //分页时，带入search参数
            var pars = search(1);
            kkd_data_init('/rank?page=' + p + "&" + pars);
        }
        else kkd_data_init('/rank?'+p);
    }
</script>