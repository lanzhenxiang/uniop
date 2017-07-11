                        <li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright"><?php echo isset($config['vpcName']) ? $config['vpcName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">所在子网：</span>
                            <span class="deright"><?php echo isset($config['netName']) ? $config['netName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">主机名称：</span>
                            <span class="deright"><?php echo isset($config['ecsName']) ? $config['ecsName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">添加网络：</span>
                            <span class="deright" ><?php echo isset($config['subnetCode2']) ? $config['subnetCode2'] : ''; ?></span>
                        </li>