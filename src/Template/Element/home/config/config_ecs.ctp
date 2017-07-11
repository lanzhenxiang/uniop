                        <li>
                            <span class="deleft">计费周期：</span>
                            <span class="deright" id="span_billCycle"><?php echo isset($config['billCycleName']) ? $config['billCycleName'] : ''; ?> </span>
                        </li>

                        <li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright"><?php echo isset($config['dyName']) ? $config['dyName'] : ''; ?></span>
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
                            <span class="deleft">主机名称：</span>
                            <span class="deright"><?php echo isset($config['ecsName']) ? $config['ecsName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">计算能力：</span>
                            <span class="deright">----------------------</span>
                            
                        </li>
                        <li>
                            <span class="deleft">CPU：</span>
                            <span class="deright"><?php echo isset($config['cpu']) ? $config['cpu'] : ''; ?>核</span>
                        </li>
                        <li>
                            <span class="deleft">内存：</span>
                            <span class="deright"><?php echo isset($config['rom']) ? $config['rom'] : ''; ?>GB</span>
                        </li>
                        <li>
                            <span class="deleft">系统镜像：</span>
                            <span class="deright"><?php echo isset($config['imageName']) ? $config['imageName'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">添加网络：</span>
                            <span class="deright" ><?php echo isset($config['subnetCode2']) ? $config['subnetCode2'] : ''; ?></span>
                        </li>
                        <li style="display: none">
                            <span class="deleft">购买量：</span>
                            <span class="deright"> {{num}} 台</span>
                        </li>
                        <li>
                            <span class="deleft">单台原价明细：</span>
                            <span class="deright">----------------------</span>
                        </li>
                        <li>
                            <span class="deleft">计算能力：</span>
                            <span class="deright" id="instancePay"><?php echo isset($config['instance_price']) ? $config['instance_price'] : ''; ?></span>
                        </li>
                        <li>
                            <span class="deleft">系统镜像：</span>
                            <span class="deright" id="imagePay"><?php echo isset($config['image_price']) ? $config['image_price'] : ''; ?></span>
                        </li>