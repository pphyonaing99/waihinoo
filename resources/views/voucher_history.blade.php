<link rel="stylesheet" type="text/css" href="{{asset('css/table.css')}}"> 
<link rel="stylesheet" type="text/css" href="{{asset('css/card.css')}}"> 
<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> 
<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">  
<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">  
<link rel="stylesheet" type="text/css" href="{{asset('css/dataTables.bootstrap4.css')}}">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.3.1/css/all.min.css" rel="stylesheet">

<style>
    section .container, section:first-child{
        padding-top: 20px;
    }
</style>

<section id="plan-features">
    
    <form method="post" action="{{ route('voucherHistory') }}">
        @csrf
        <div class="row">
            <div class="col-md-4 offset-md-2">
                <div class="form-group">
                    <label>Daily</label>
                    <input type="date" class="form-control" name="start_timetick" id="daily" />
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Monthly</label>
                    <select class="form-control" id="monthly" onchange="getMonthlyProfit(this.value)">
                        <option value="1">January</option>
                        <option value="2">February</option>
                        <option value="3">March</option>
                        <option value="4">April</option>
                        <option value="5">May</option>
                        <option value="6">June</option>
                        <option value="7">July</option>
                        <option value="8">Auguest</option>
                        <option value="9">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>
            </div>
            
        </div>        
    </form>
    
    <div class="row ml-2 mr-2">
        <div class="col-xl-4 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total Sales</h5>
                        <span class="h2 font-weight-bold mb-0 text-dark" style="font-size: 25px;" id="total_sales">{{ number_format($total_sales[0]->total) }} ks</span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                            <i class="fas fa-balance-scale"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                    <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Phone Sales</h5>
                        <span class="h2 font-weight-bold mb-0 text-dark" style="font-size: 25px;" id="product_profit" >{{ number_format($product_profits[0]->total) }} ks</span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                            <i class="fas fa-mobile"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                    <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Accessory Sales</h5>
                            <span class="h2 font-weight-bold mb-0 text-dark" style="font-size: 25px;" id="accessory_profit">{{ number_format($accessory_profits[0]->total) }} ks</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                                <i class="fas fa-headphones"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                    <span class="text-nowrap">Since last month</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row ml-2 mr-2 mt-2">
        <div class="col-xl-4 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total Profit</h5>
                        <span class="h2 font-weight-bold mb-0 text-dark" style="font-size: 25px;" id="total_revenue">{{ number_format($total_revenues) }} ks</span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                            <i class="fas fa-balance-scale"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <!--<span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>-->
                    <!--<span class="text-nowrap">Since last month</span>-->
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Phone Sales Profit</h5>
                        <span class="h2 font-weight-bold mb-0 text-dark" style="font-size: 25px;" id="ph_sale_revenue">{{ number_format($ph_sales_revenues) }} ks</span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                            <i class="fas fa-mobile"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <!--<span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>-->
                    <!--<span class="text-nowrap">Since last month</span>-->
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Accessory Sales Profit</h5>
                            <span class="h2 font-weight-bold mb-0 text-dark" style="font-size: 25px;" id="acc_sale_revenue">{{ number_format($acc_sales_revenues) }} ks</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape text-white rounded-circle shadow" style="background-color:#473C70;">
                                <i class="fas fa-headphones"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-muted text-sm">
                    <!--<span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>-->
                    <!--<span class="text-nowrap">Since last month</span>-->
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container py-0">
        
        <form method="post" action="{{ route('voucherHistory') }}">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_timetick" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="end_timetick" />
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="submit" value="Search" class="btn btn-primary" style="margin-top:30px;" />
                </div>
                
            </div>        
        </form>
        
    </div>
    
    <div class="container">

        <a href=""><i class="fas fa-sync"></i></a>
        <table id="plan-matrix" class="table-zebra table-zebra-dark more-spacing">
            <thead style="color: white;">
                <tr>
                    <th style="text-align: center;" class="plan-matrix-label">Voucher Number</th>
                    <th style="text-align: center;" class="plan-matrix-details">Sold By</th>
                    <th style="text-align: center;" class="plan-matrix-details">Payment Type</th>
                    <th style="text-align: center;" class="plan-matrix-details">Total Amount</th>
                    <th style="text-align: center;" class="plan-matrix-details">Details</th>
                </tr>
            </thead>
            <tbody style="color: white;" id="voucher">
                @foreach($vouchers as $voucher)
                <tr>
                    <td class="plan-matrix-label" style="text-align: center;">{{ $voucher->voucher_number }}</td>
                    <td style="text-align: center;">{{ $voucher->sold_by }}</td>
                    <td style="text-align: center;">{{ $voucher->payment_type }}</td>
                    <td style="text-align: center;">{{ $voucher->total_amount }}</td>
                    <td style="text-align: center;"><a href="{{ route('getVoucherDetails',$voucher->id) }}" class="btn btn-primary" style="color: white;">Details</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
 <script src="{{asset('js/jquery.min.js')}}"></script>
 <script src="{{asset('js/jquery.datatable.js')}}"></script>
 <script src="{{asset('js/bootstrap.min.js')}}"></script>
 <script src="{{asset('js/dataTables.bootstrap4.min.js')}}"></script>
 <script>
  $(document).ready( function () {
    
    let mydate=window.document.getElementById("daily");
    let olddate=mydate.value;
    let isChanged = function(){
      if(mydate.value!== olddate){
        olddate=mydate.value;
        return true;
      };
      return false;
    };
    mydate.addEventListener("blur", function(){
      if(isChanged())
        $.ajax({

           type:'POST',

           url:'/getDailyProfit',

           dataType:'json',

           data:{
           	"_token":"{{csrf_token()}}",
           	"date":olddate, 
           },

           success:function(data){
               var html = '';
             	$('#total_sales').text(data.total_sales.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#product_profit').text(data.product_profits.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#accessory_profit').text(data.accessory_profits.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#ph_sale_revenue').text(data.ph_sales_revenues.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#acc_sale_revenue').text(data.acc_sales_revenues.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#total_revenue').text(data.total_revenues.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	
             	$.each(data.voucher,function(i,v){
             	    var show_url = "{{ url('/getVoucherDetails') }}/"+v.id;
             	    html += `<tr>
                            <td class="plan-matrix-label" style="text-align: center;">${v.voucher_number}</td>
                            <td style="text-align: center;">${ v.sold_by }</td>
                            <td style="text-align: center;">${ v.payment_type }</td>
                            <td style="text-align: center;">${ v.voucher_grand_total }</td>
                            <td style="text-align: center;"><a href="${show_url}" class="btn btn-primary" style="color: white;">Details</a></td>
                        </tr>`;
             	})
             	$('#voucher').html(html);
           }

        });
    });
    
    getMonthlyProfit = (month) => {
        $.ajax({

           type:'POST',

           url:'/getMonthlyProfit',

           dataType:'json',

           data:{
           	"_token":"{{csrf_token()}}",
           	"month":month, 
           },

           success:function(data){
               console.log(data);
               var html = '';
             	$('#total_sales').text(data.total_sales.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#product_profit').text(data.product_profits.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#accessory_profit').text(data.accessory_profits.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#ph_sale_revenue').text(data.ph_sales_revenues.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#acc_sale_revenue').text(data.acc_sales_revenues.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	$('#total_revenue').text(data.total_revenues.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+' ks');
             	
             	$.each(data.voucher,function(i,v){
             	    var show_url = "{{ url('/getVoucherDetails') }}/"+v.id;
             	    html += `<tr>
                            <td class="plan-matrix-label" style="text-align: center;">${v.voucher_number}</td>
                            <td style="text-align: center;">${ v.sold_by }</td>
                            <td style="text-align: center;">${ v.payment_type }</td>
                            <td style="text-align: center;">${ v.voucher_grand_total }</td>
                            <td style="text-align: center;"><a href="${show_url}" class="btn btn-primary" style="color: white;">Details</a></td>
                        </tr>`;
             	})
             	$('#voucher').html(html);
           }

        });
    }
    
    $('#plan-matrix').DataTable({
        
        order: [ [0, 'desc'] ]
    })
});
</script>
