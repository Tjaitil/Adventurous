           <div id="workers">
                <ul>
                    <?php if(!count($data) > 0): ?>
                        <li> No workers available </li>
                    <?php endif; ?>
                    <?php foreach($data as $key): var_dump($data);?>
                    <li>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG"). $key['type'] . '.jpg'?>" style="length:50px; width:50px" />
                            <figcaption><?php echo ucfirst($key['type']); ?>,
                                <?php echo ($key['level'] > 0) ? 'level ' . $key['level'] . ',' : "";?> Value 350</figcaption>
                        </figure>
                    <button onclick="recruitWorker('<?php echo $key['type'];?>', '<?php echo $key['level'];?>');"> Recrute </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>