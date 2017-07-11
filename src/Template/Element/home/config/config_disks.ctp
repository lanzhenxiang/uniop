
                        <li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright"><?php echo isset($config['dyName']) ? $config['dyName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">名称：</span>
                            <span class="deright"><?php echo isset($config['disksName']) ? $config['disksName'] : ''; ?></span>
                        </li>
                        
                        <li>
                            <span class="deleft">容量：</span>
                            <span class="deright"><?php echo isset($config['size']) ? $config['size'] : ''; ?>GB</span>
                        </li>
                        <li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright"><?php echo isset($config['vpcName']) ? $config['vpcName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">所在子网：</span>
                            <span class="deright"><?php echo isset($config['netName']) ? $config['netName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">挂载主机：</span>
                            <span class="deright"><?php echo isset($config['hostsName']) ? $config['hostsName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright"><?php echo isset($config['billCycleName']) ? $config['billCycleName'] : ''; ?> </span>
                        </li>
                        <li>
                            <span class="deleft">单台原价：</span>
                            <span class="deright"><?php echo isset($config['price']) ? $config['price'] : 0; ?><?php echo isset($config['unit']) ? $config['unit'] : 0; ?>·GB</span>
                        </li>
                        <li>
                            <span class="deleft">单台总价：</span>
                            <span class="deright"><?php echo isset($config['totalPrice']) ? $config['totalPrice'] : 0; ?><?php echo isset($config['totalUnit']) ? $config['totalUnit'] : 0; ?></span>
                        </li>