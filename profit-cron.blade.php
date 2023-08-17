<?php

    $dbServername = "";
    $dbUsername = "";
    $dbPassword = "";
    $dbName = "";
    
    $conn = mysqli_connect($dbServername, $dbUsername, $dbPassword, $dbName);
    
    if(!$conn){
        die("Connection Could not be established");
    }
    
    $invests_sql = "SELECT * FROM invests WHERE status='ongoing' and interest_type='percentage';";
    $invests_result = mysqli_query($conn, $invests_sql);
    $invests_resultCheck = mysqli_num_rows($invests_result);
    
    if($invests_resultCheck > 0){
        while($invests_row = mysqli_fetch_assoc($invests_result)){
            $invests_id = $invests_row['id'];
            $invests_user_id = $invests_row['user_id'];
            $invests_schema_id = $invests_row['schema_id'];
            $invests_tran_id = $invests_row['transaction_id'];
            $invests_invest_amount = $invests_row['invest_amount'];
            $invests_already_return_profit = $invests_row['already_return_profit'];
            $invests_total_profit_amount = $invests_row['total_profit_amount'];
            $invests_last_profit_time = $invests_row['last_profit_time'];
            $invests_next_profit_time = $invests_row['next_profit_time'];
            $invests_interest = $invests_row['interest'];
            $invests_interest_type = $invests_row['interest_type'];
            $invests_return_type = $invests_row['return_type'];
            $invests_number_of_period = $invests_row['number_of_period'];
            $invests_period_hours = $invests_row['period_hours'];
            $invests_return_counter = $invests_row['return_counter'];
            
            $lst_pro_time = date('Y-m-d H:i:s');
            $next_profit_time = date("Y-m-d H:i:s", strtotime("+1 hours"));
            
            $period_calc_return_counter = $invests_number_of_period * $invests_period_hours;
            $return_counter = $period_calc_return_counter - 1;
            
            $percent_return_per_hour = $invests_interest / $invests_period_hours;
            $total_investment_return = ($invests_interest / 100) * $invests_invest_amount;
            $amount_return_per_hour = round((($percent_return_per_hour / 100) * $invests_invest_amount), 2);
            $already_return_proft = $invests_already_return_profit + $amount_return_per_hour;
            
            $cont_return_counter = $invests_return_counter -1;
            
            $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            $trnx = strtoupper("trx".substr(str_shuffle($str_result), 0, 10));
            
            if($invests_return_counter == "-1" && $invests_interest_type == "percentage"){
                $invest_update_sql = "UPDATE invests SET already_return_profit='$amount_return_per_hour', total_profit_amount='$total_investment_return', last_profit_time='$lst_pro_time', 
                next_profit_time='$next_profit_time', return_counter='$return_counter' WHERE id='$invests_id';";
                if(mysqli_query($conn, $invest_update_sql)){
                    echo "Updated<br>";
                    $trnx_update_sql = "INSERT into transactions (user_id, from_model, tnx, description, amount, type, charge, final_amount, method, manual_field_data, approval_cause, status, created_at, updated_at)
                    VALUES ('$invests_user_id', 'User', '$trnx', 'Plan Profit', '$amount_return_per_hour', 'interest', '0', '$amount_return_per_hour', 'system', '[]', 'none', 'success', '$lst_pro_time', '$lst_pro_time');";
                    if(mysqli_query($conn, $trnx_update_sql)){
                        echo "transaction inserted<br>";
                        $users_sql = "SELECT * FROM users WHERE id='$invests_user_id';";
                        $users_result = mysqli_query($conn, $users_sql);
                        $users_resultCheck = mysqli_num_rows($users_result);
                        
                        if($users_resultCheck > 0){
                            while($users_row = mysqli_fetch_assoc($users_result)){
                                $users_first_name = $users_row['first_name'];
                                $users_last_name = $users_row['last_name'];
                                $users_name = $users_row['username'];
                                $users_email = $users_row['email'];
                                $users_balance = $users_row['balance'];
                                $users_profit_balance = $users_row['profit_balance'];
                                
                                $users_new_balance = $users_balance + $amount_return_per_hour;
                                $users_new_profit_blance = $users_profit_balance + $amount_return_per_hour;
                                
                                $users_update_sql = "UPDATE users SET profit_balance='$users_new_profit_blance' WHERE id='$invests_user_id';";
                                // $users_update_sql = "UPDATE users SET balance='$users_new_balance', profit_balance='$users_new_profit_blance' WHERE id='$invests_user_id';";
                                if(mysqli_query($conn, $users_update_sql)){
                                    echo "users updated<br>";
                                }else{
                                    echo "user not updated<br>";
                                }
                                
                            }
                        }
                    }else{
                        echo "transaction not inserted<br>";
                    }
                }else{
                    echo "Not Updated<br>";
                }
            }
            if($invests_return_counter != "-1" && $invests_return_counter != "0" && $invests_interest_type == "percentage"){
                $invest_update_sql = "UPDATE invests SET already_return_profit='$already_return_proft', last_profit_time='$lst_pro_time', next_profit_time='$next_profit_time', 
                return_counter='$cont_return_counter' WHERE id='$invests_id';";
                if(mysqli_query($conn, $invest_update_sql)){
                    echo "Updated<br>";
                    $trnx_update_sql = "INSERT into transactions (user_id, from_model, tnx, description, amount, type, charge, final_amount, method, manual_field_data, approval_cause, status, created_at, updated_at)
                    VALUES ('$invests_user_id', 'User', '$trnx', 'Plan Profit', '$amount_return_per_hour', 'interest', '0', '$amount_return_per_hour', 'system', '[]', 'none', 'success', '$lst_pro_time', '$lst_pro_time');";
                    if(mysqli_query($conn, $trnx_update_sql)){
                        echo "transaction inserted<br>";
                        $users_sql = "SELECT * FROM users WHERE id='$invests_user_id';";
                        $users_result = mysqli_query($conn, $users_sql);
                        $users_resultCheck = mysqli_num_rows($users_result);
                        
                        if($users_resultCheck > 0){
                            while($users_row = mysqli_fetch_assoc($users_result)){
                                $users_first_name = $users_row['first_name'];
                                $users_last_name = $users_row['last_name'];
                                $users_name = $users_row['username'];
                                $users_email = $users_row['email'];
                                $users_balance = $users_row['balance'];
                                $users_profit_balance = $users_row['profit_balance'];
                                
                                $users_new_balance = $users_balance + $amount_return_per_hour;
                                $users_new_profit_blance = $users_profit_balance + $amount_return_per_hour;
                                
                                $users_update_sql = "UPDATE users SET profit_balance='$users_new_profit_blance' WHERE id='$invests_user_id';";
                                if(mysqli_query($conn, $users_update_sql)){
                                    echo "users updated<br>";
                                }else{
                                    echo "user not updated<br>";
                                }
                                
                            }
                        }
                    }else{
                        echo "transaction not inserted<br>";
                    }
                }else{
                    echo "Not Updated<br>";
                }
            }
            if($invests_return_counter == "0" && $invests_interest_type == "percentage"){
                $invest_update_sql = "UPDATE invests SET last_profit_time='$lst_pro_time', next_profit_time='$next_profit_time', return_counter='$cont_return_counter', status='completed' 
                WHERE id='$invests_id';";
                if(mysqli_query($conn, $invest_update_sql)){
                    echo "Updated<br>";
                    $trnx_update_sql = "INSERT into transactions (user_id, from_model, tnx, description, amount, type, charge, final_amount, method, manual_field_data, approval_cause, status, created_at, updated_at)
                    VALUES ('$invests_user_id', 'User', '$trnx', 'Plan Profit', '$amount_return_per_hour', 'interest', '0', '$amount_return_per_hour', 'system', '[]', 'none', 'success', '$lst_pro_time', '$lst_pro_time');";
                    if(mysqli_query($conn, $trnx_update_sql)){
                        echo "transaction inserted<br>";
                    }else{
                        echo "transaction not inserted<br>";
                    }
                    
                    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                    $cap_trnx = strtoupper("trx".substr(str_shuffle($str_result), 0, 10));
                    
                    $trnx_cap_update_sql = "INSERT into transactions (user_id, from_model, tnx, description, amount, type, charge, final_amount, method, manual_field_data, approval_cause, status, created_at, updated_at)
                    VALUES ('$invests_user_id', 'User', '$cap_trnx', 'Capital Return', '$invests_invest_amount', 'investment', '0', '$invests_invest_amount', 'system', '[]', 'none', 'success', '$lst_pro_time', '$lst_pro_time');";
                    if(mysqli_query($conn, $trnx_cap_update_sql)){
                        echo "capital return transaction inserted<br>";
                        $users_sql = "SELECT * FROM users WHERE id='$invests_user_id';";
                        $users_result = mysqli_query($conn, $users_sql);
                        $users_resultCheck = mysqli_num_rows($users_result);
                        
                        if($users_resultCheck > 0){
                            while($users_row = mysqli_fetch_assoc($users_result)){
                                $users_first_name = $users_row['first_name'];
                                $users_last_name = $users_row['last_name'];
                                $users_name = $users_row['username'];
                                $users_email = $users_row['email'];
                                $users_balance = $users_row['balance'];
                                $users_profit_balance = $users_row['profit_balance'];
                                
                                $users_new_balance = $users_balance + $invests_invest_amount;
                                $users_new_profit_blance = $users_profit_balance + $invests_invest_amount;
                                
                                $users_update_sql = "UPDATE users SET profit_balance='$users_new_profit_blance' WHERE id='$invests_user_id';";
                                if(mysqli_query($conn, $users_update_sql)){
                                    echo "users updated<br>";
                                }else{
                                    echo "user not updated<br>";
                                }
                                
                            }
                        }
                    }else{
                        echo "capital return transaction not inserted<br>";
                    }
                }else{
                    echo "Not Updated<br>";
                }
            }
        }
    }

?>
