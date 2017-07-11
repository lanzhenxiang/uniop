
                        <li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright"><?php echo isset($config['dyName']) ? $config['dyName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">路由器：</span>
                            <span class="deright"><?php echo isset($config['routerName']) ? $config['routerName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">VPC名：</span>
                            <span class="deright"><?php echo isset($config['vpcName']) ? $config['vpcName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">VPC地址：</span>
                            <span class="deright"><?php echo isset($config['cidr']) ? $config['cidr'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright"><?php echo isset($config['billCycleName']) ? $config['billCycleName'] : ''; ?> </span>
                        </li>
                        <li>
                            <span class="deleft">单台原价：</span>
                            <span class="deright"><?php echo isset($config['price']) ? $config['price'] : 0; ?><?php echo isset($config['unit']) ? $config['unit'] : 0; ?></span>
                        </li>