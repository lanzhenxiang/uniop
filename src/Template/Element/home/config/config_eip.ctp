
                        <li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright"><?php echo isset($config['dyName']) ? $config['dyName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright"><?php echo isset($config['vpcName']) ? $config['vpcName'] : ''; ?></span>
                        </li>
                        
                        <li>
                            <span class="deleft">主机名称：</span>
                            <span class="deright"><?php echo isset($config['eipName']) ? $config['eipName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright"><?php echo isset($config['billCycleName']) ? $config['billCycleName'] : ''; ?> </span>
                        </li>
                        <li>
                            <span class="deleft">单M原价：</span>
                            <span class="deright"><?php echo isset($config['price']) ? $config['price'] : 0; ?><?php echo isset($config['unit']) ? $config['unit'] : '元'; ?>.M</span>
                        </li>
                        <li>
                            <span class="deleft">单个原价：</span>
                            <span class="deright"><?php echo isset($config['price_total']) ? $config['price_total'] : 0; ?><?php echo isset($config['unit']) ? $config['unit'] : '元'; ?></span>
                        </li>