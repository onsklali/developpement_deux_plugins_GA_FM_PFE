<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: google/cloud/dataproc/v1/workflow_templates.proto

namespace GPBMetadata\Google\Cloud\Dataproc\V1;

class WorkflowTemplates
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Google\Api\Annotations::initOnce();
        \GPBMetadata\Google\Api\Client::initOnce();
        \GPBMetadata\Google\Api\FieldBehavior::initOnce();
        \GPBMetadata\Google\Api\Resource::initOnce();
        \GPBMetadata\Google\Cloud\Dataproc\V1\Clusters::initOnce();
        \GPBMetadata\Google\Cloud\Dataproc\V1\Jobs::initOnce();
        \GPBMetadata\Google\Longrunning\Operations::initOnce();
        \GPBMetadata\Google\Protobuf\GPBEmpty::initOnce();
        \GPBMetadata\Google\Protobuf\Timestamp::initOnce();
        $pool->internalAddGeneratedFile(hex2bin(
            "0adf3a0a31676f6f676c652f636c6f75642f6461746170726f632f76312f" .
            "776f726b666c6f775f74656d706c617465732e70726f746f1218676f6f67" .
            "6c652e636c6f75642e6461746170726f632e76311a17676f6f676c652f61" .
            "70692f636c69656e742e70726f746f1a1f676f6f676c652f6170692f6669" .
            "656c645f6265686176696f722e70726f746f1a19676f6f676c652f617069" .
            "2f7265736f757263652e70726f746f1a27676f6f676c652f636c6f75642f" .
            "6461746170726f632f76312f636c7573746572732e70726f746f1a23676f" .
            "6f676c652f636c6f75642f6461746170726f632f76312f6a6f62732e7072" .
            "6f746f1a23676f6f676c652f6c6f6e6772756e6e696e672f6f7065726174" .
            "696f6e732e70726f746f1a1b676f6f676c652f70726f746f6275662f656d" .
            "7074792e70726f746f1a1f676f6f676c652f70726f746f6275662f74696d" .
            "657374616d702e70726f746f22cd050a10576f726b666c6f7754656d706c" .
            "617465120f0a0269641802200128094203e0410212110a046e616d651801" .
            "200128094203e0410312140a0776657273696f6e1803200128054203e041" .
            "0112340a0b6372656174655f74696d6518042001280b321a2e676f6f676c" .
            "652e70726f746f6275662e54696d657374616d704203e0410312340a0b75" .
            "70646174655f74696d6518052001280b321a2e676f6f676c652e70726f74" .
            "6f6275662e54696d657374616d704203e04103124b0a066c6162656c7318" .
            "062003280b32362e676f6f676c652e636c6f75642e6461746170726f632e" .
            "76312e576f726b666c6f7754656d706c6174652e4c6162656c73456e7472" .
            "794203e04101124b0a09706c6163656d656e7418072001280b32332e676f" .
            "6f676c652e636c6f75642e6461746170726f632e76312e576f726b666c6f" .
            "7754656d706c617465506c6163656d656e744203e0410212370a046a6f62" .
            "7318082003280b32242e676f6f676c652e636c6f75642e6461746170726f" .
            "632e76312e4f7264657265644a6f624203e0410212440a0a706172616d65" .
            "7465727318092003280b322b2e676f6f676c652e636c6f75642e64617461" .
            "70726f632e76312e54656d706c617465506172616d657465724203e04101" .
            "1a2d0a0b4c6162656c73456e747279120b0a036b6579180120012809120d" .
            "0a0576616c75651802200128093a0238013aca01ea41c6010a2864617461" .
            "70726f632e676f6f676c65617069732e636f6d2f576f726b666c6f775465" .
            "6d706c617465124970726f6a656374732f7b70726f6a6563747d2f726567" .
            "696f6e732f7b726567696f6e7d2f776f726b666c6f7754656d706c617465" .
            "732f7b776f726b666c6f775f74656d706c6174657d124d70726f6a656374" .
            "732f7b70726f6a6563747d2f6c6f636174696f6e732f7b6c6f636174696f" .
            "6e7d2f776f726b666c6f7754656d706c617465732f7b776f726b666c6f77" .
            "5f74656d706c6174657d200122b4010a19576f726b666c6f7754656d706c" .
            "617465506c6163656d656e7412430a0f6d616e616765645f636c75737465" .
            "7218012001280b32282e676f6f676c652e636c6f75642e6461746170726f" .
            "632e76312e4d616e61676564436c7573746572480012450a10636c757374" .
            "65725f73656c6563746f7218022001280b32292e676f6f676c652e636c6f" .
            "75642e6461746170726f632e76312e436c757374657253656c6563746f72" .
            "4800420b0a09706c6163656d656e7422e3010a0e4d616e61676564436c75" .
            "7374657212190a0c636c75737465725f6e616d651802200128094203e041" .
            "02123c0a06636f6e66696718032001280b32272e676f6f676c652e636c6f" .
            "75642e6461746170726f632e76312e436c7573746572436f6e6669674203" .
            "e0410212490a066c6162656c7318042003280b32342e676f6f676c652e63" .
            "6c6f75642e6461746170726f632e76312e4d616e61676564436c75737465" .
            "722e4c6162656c73456e7472794203e041011a2d0a0b4c6162656c73456e" .
            "747279120b0a036b6579180120012809120d0a0576616c75651802200128" .
            "093a02380122b5010a0f436c757374657253656c6563746f7212110a047a" .
            "6f6e651801200128094203e0410112590a0e636c75737465725f6c616265" .
            "6c7318022003280b323c2e676f6f676c652e636c6f75642e646174617072" .
            "6f632e76312e436c757374657253656c6563746f722e436c75737465724c" .
            "6162656c73456e7472794203e041021a340a12436c75737465724c616265" .
            "6c73456e747279120b0a036b6579180120012809120d0a0576616c756518" .
            "02200128093a02380122de050a0a4f7264657265644a6f6212140a077374" .
            "65705f69641801200128094203e0410212390a0a6861646f6f705f6a6f62" .
            "18022001280b32232e676f6f676c652e636c6f75642e6461746170726f63" .
            "2e76312e4861646f6f704a6f62480012370a09737061726b5f6a6f621803" .
            "2001280b32222e676f6f676c652e636c6f75642e6461746170726f632e76" .
            "312e537061726b4a6f624800123b0a0b7079737061726b5f6a6f62180420" .
            "01280b32242e676f6f676c652e636c6f75642e6461746170726f632e7631" .
            "2e5079537061726b4a6f62480012350a08686976655f6a6f621805200128" .
            "0b32212e676f6f676c652e636c6f75642e6461746170726f632e76312e48" .
            "6976654a6f62480012330a077069675f6a6f6218062001280b32202e676f" .
            "6f676c652e636c6f75642e6461746170726f632e76312e5069674a6f6248" .
            "00123a0a0b737061726b5f725f6a6f62180b2001280b32232e676f6f676c" .
            "652e636c6f75642e6461746170726f632e76312e537061726b524a6f6248" .
            "00123e0a0d737061726b5f73716c5f6a6f6218072001280b32252e676f6f" .
            "676c652e636c6f75642e6461746170726f632e76312e537061726b53716c" .
            "4a6f62480012390a0a70726573746f5f6a6f62180c2001280b32232e676f" .
            "6f676c652e636c6f75642e6461746170726f632e76312e50726573746f4a" .
            "6f62480012450a066c6162656c7318082003280b32302e676f6f676c652e" .
            "636c6f75642e6461746170726f632e76312e4f7264657265644a6f622e4c" .
            "6162656c73456e7472794203e0410112400a0a7363686564756c696e6718" .
            "092001280b32272e676f6f676c652e636c6f75642e6461746170726f632e" .
            "76312e4a6f625363686564756c696e674203e0410112220a157072657265" .
            "717569736974655f737465705f696473180a200328094203e041011a2d0a" .
            "0b4c6162656c73456e747279120b0a036b6579180120012809120d0a0576" .
            "616c75651802200128093a023801420a0a086a6f625f74797065229d010a" .
            "1154656d706c617465506172616d6574657212110a046e616d6518012001" .
            "28094203e0410212130a066669656c64731802200328094203e041021218" .
            "0a0b6465736372697074696f6e1803200128094203e0410112460a0a7661" .
            "6c69646174696f6e18042001280b322d2e676f6f676c652e636c6f75642e" .
            "6461746170726f632e76312e506172616d6574657256616c69646174696f" .
            "6e4203e0410122a1010a13506172616d6574657256616c69646174696f6e" .
            "123a0a05726567657818012001280b32292e676f6f676c652e636c6f7564" .
            "2e6461746170726f632e76312e526567657856616c69646174696f6e4800" .
            "123b0a0676616c75657318022001280b32292e676f6f676c652e636c6f75" .
            "642e6461746170726f632e76312e56616c756556616c69646174696f6e48" .
            "0042110a0f76616c69646174696f6e5f7479706522270a0f526567657856" .
            "616c69646174696f6e12140a07726567657865731801200328094203e041" .
            "0222260a0f56616c756556616c69646174696f6e12130a0676616c756573" .
            "1801200328094203e0410222af050a10576f726b666c6f774d6574616461" .
            "746112150a0874656d706c6174651801200128094203e0410312140a0776" .
            "657273696f6e1802200128054203e0410312470a0e6372656174655f636c" .
            "757374657218032001280b322a2e676f6f676c652e636c6f75642e646174" .
            "6170726f632e76312e436c75737465724f7065726174696f6e4203e04103" .
            "123b0a05677261706818042001280b32272e676f6f676c652e636c6f7564" .
            "2e6461746170726f632e76312e576f726b666c6f7747726170684203e041" .
            "0312470a0e64656c6574655f636c757374657218052001280b322a2e676f" .
            "6f676c652e636c6f75642e6461746170726f632e76312e436c7573746572" .
            "4f7065726174696f6e4203e0410312440a05737461746518062001280e32" .
            "302e676f6f676c652e636c6f75642e6461746170726f632e76312e576f72" .
            "6b666c6f774d657461646174612e53746174654203e0410312190a0c636c" .
            "75737465725f6e616d651807200128094203e04103124e0a0a706172616d" .
            "657465727318082003280b323a2e676f6f676c652e636c6f75642e646174" .
            "6170726f632e76312e576f726b666c6f774d657461646174612e50617261" .
            "6d6574657273456e74727912330a0a73746172745f74696d651809200128" .
            "0b321a2e676f6f676c652e70726f746f6275662e54696d657374616d7042" .
            "03e0410312310a08656e645f74696d65180a2001280b321a2e676f6f676c" .
            "652e70726f746f6275662e54696d657374616d704203e0410312190a0c63" .
            "6c75737465725f75756964180b200128094203e041031a310a0f50617261" .
            "6d6574657273456e747279120b0a036b6579180120012809120d0a057661" .
            "6c75651802200128093a02380122380a055374617465120b0a07554e4b4e" .
            "4f574e1000120b0a0750454e44494e471001120b0a0752554e4e494e4710" .
            "0212080a04444f4e45100322540a10436c75737465724f7065726174696f" .
            "6e12190a0c6f7065726174696f6e5f69641801200128094203e041031212" .
            "0a056572726f721802200128094203e0410312110a04646f6e6518032001" .
            "28084203e04103224b0a0d576f726b666c6f774772617068123a0a056e6f" .
            "64657318012003280b32262e676f6f676c652e636c6f75642e6461746170" .
            "726f632e76312e576f726b666c6f774e6f64654203e0410322a3020a0c57" .
            "6f726b666c6f774e6f646512140a07737465705f69641801200128094203" .
            "e0410312220a157072657265717569736974655f737465705f6964731802" .
            "200328094203e0410312130a066a6f625f69641803200128094203e04103" .
            "12440a05737461746518052001280e32302e676f6f676c652e636c6f7564" .
            "2e6461746170726f632e76312e576f726b666c6f774e6f64652e4e6f6465" .
            "53746174654203e0410312120a056572726f721806200128094203e04103" .
            "226a0a094e6f64655374617465121a0a164e4f44455f53544154455f554e" .
            "5350454349464945441000120b0a07424c4f434b45441001120c0a085255" .
            "4e4e41424c451002120b0a0752554e4e494e471003120d0a09434f4d504c" .
            "455445441004120a0a064641494c4544100522a4010a1d43726561746557" .
            "6f726b666c6f7754656d706c6174655265717565737412400a0670617265" .
            "6e741801200128094230e04102fa412a12286461746170726f632e676f6f" .
            "676c65617069732e636f6d2f576f726b666c6f7754656d706c6174651241" .
            "0a0874656d706c61746518022001280b322a2e676f6f676c652e636c6f75" .
            "642e6461746170726f632e76312e576f726b666c6f7754656d706c617465" .
            "4203e0410222720a1a476574576f726b666c6f7754656d706c6174655265" .
            "7175657374123e0a046e616d651801200128094230e04102fa412a0a2864" .
            "61746170726f632e676f6f676c65617069732e636f6d2f576f726b666c6f" .
            "7754656d706c61746512140a0776657273696f6e1802200128054203e041" .
            "0122ad020a22496e7374616e7469617465576f726b666c6f7754656d706c" .
            "61746552657175657374123e0a046e616d651801200128094230e04102fa" .
            "412a0a286461746170726f632e676f6f676c65617069732e636f6d2f576f" .
            "726b666c6f7754656d706c61746512140a0776657273696f6e1802200128" .
            "054203e0410112170a0a726571756573745f69641805200128094203e041" .
            "0112650a0a706172616d657465727318062003280b324c2e676f6f676c65" .
            "2e636c6f75642e6461746170726f632e76312e496e7374616e7469617465" .
            "576f726b666c6f7754656d706c617465526571756573742e506172616d65" .
            "74657273456e7472794203e041011a310a0f506172616d6574657273456e" .
            "747279120b0a036b6579180120012809120d0a0576616c75651802200128" .
            "093a02380122c8010a28496e7374616e7469617465496e6c696e65576f72" .
            "6b666c6f7754656d706c6174655265717565737412400a06706172656e74" .
            "1801200128094230e04102fa412a12286461746170726f632e676f6f676c" .
            "65617069732e636f6d2f576f726b666c6f7754656d706c61746512410a08" .
            "74656d706c61746518022001280b322a2e676f6f676c652e636c6f75642e" .
            "6461746170726f632e76312e576f726b666c6f7754656d706c6174654203" .
            "e0410212170a0a726571756573745f69641803200128094203e041012262" .
            "0a1d557064617465576f726b666c6f7754656d706c617465526571756573" .
            "7412410a0874656d706c61746518012001280b322a2e676f6f676c652e63" .
            "6c6f75642e6461746170726f632e76312e576f726b666c6f7754656d706c" .
            "6174654203e041022291010a1c4c697374576f726b666c6f7754656d706c" .
            "617465735265717565737412400a06706172656e741801200128094230e0" .
            "4102fa412a12286461746170726f632e676f6f676c65617069732e636f6d" .
            "2f576f726b666c6f7754656d706c61746512160a09706167655f73697a65" .
            "1802200128054203e0410112170a0a706167655f746f6b656e1803200128" .
            "094203e041012281010a1d4c697374576f726b666c6f7754656d706c6174" .
            "6573526573706f6e736512420a0974656d706c6174657318012003280b32" .
            "2a2e676f6f676c652e636c6f75642e6461746170726f632e76312e576f72" .
            "6b666c6f7754656d706c6174654203e04103121c0a0f6e6578745f706167" .
            "655f746f6b656e1802200128094203e0410322750a1d44656c657465576f" .
            "726b666c6f7754656d706c61746552657175657374123e0a046e616d6518" .
            "01200128094230e04102fa412a0a286461746170726f632e676f6f676c65" .
            "617069732e636f6d2f576f726b666c6f7754656d706c61746512140a0776" .
            "657273696f6e1802200128054203e0410132e6100a17576f726b666c6f77" .
            "54656d706c61746553657276696365129b020a16437265617465576f726b" .
            "666c6f7754656d706c61746512372e676f6f676c652e636c6f75642e6461" .
            "746170726f632e76312e437265617465576f726b666c6f7754656d706c61" .
            "7465526571756573741a2a2e676f6f676c652e636c6f75642e6461746170" .
            "726f632e76312e576f726b666c6f7754656d706c617465229b0182d3e493" .
            "02820122352f76312f7b706172656e743d70726f6a656374732f2a2f6c6f" .
            "636174696f6e732f2a7d2f776f726b666c6f7754656d706c617465733a08" .
            "74656d706c6174655a3f22332f76312f7b706172656e743d70726f6a6563" .
            "74732f2a2f726567696f6e732f2a7d2f776f726b666c6f7754656d706c61" .
            "7465733a0874656d706c617465da410f706172656e742c74656d706c6174" .
            "6512f4010a13476574576f726b666c6f7754656d706c61746512342e676f" .
            "6f676c652e636c6f75642e6461746170726f632e76312e476574576f726b" .
            "666c6f7754656d706c617465526571756573741a2a2e676f6f676c652e63" .
            "6c6f75642e6461746170726f632e76312e576f726b666c6f7754656d706c" .
            "617465227b82d3e493026e12352f76312f7b6e616d653d70726f6a656374" .
            "732f2a2f6c6f636174696f6e732f2a2f776f726b666c6f7754656d706c61" .
            "7465732f2a7d5a3512332f76312f7b6e616d653d70726f6a656374732f2a" .
            "2f726567696f6e732f2a2f776f726b666c6f7754656d706c617465732f2a" .
            "7dda41046e616d6512d5020a1b496e7374616e7469617465576f726b666c" .
            "6f7754656d706c617465123c2e676f6f676c652e636c6f75642e64617461" .
            "70726f632e76312e496e7374616e7469617465576f726b666c6f7754656d" .
            "706c617465526571756573741a1d2e676f6f676c652e6c6f6e6772756e6e" .
            "696e672e4f7065726174696f6e22d80182d3e493028c0122412f76312f7b" .
            "6e616d653d70726f6a656374732f2a2f6c6f636174696f6e732f2a2f776f" .
            "726b666c6f7754656d706c617465732f2a7d3a696e7374616e7469617465" .
            "3a012a5a44223f2f76312f7b6e616d653d70726f6a656374732f2a2f7265" .
            "67696f6e732f2a2f776f726b666c6f7754656d706c617465732f2a7d3a69" .
            "6e7374616e74696174653a012ada41046e616d65da410f6e616d652c7061" .
            "72616d6574657273ca41290a15676f6f676c652e70726f746f6275662e45" .
            "6d7074791210576f726b666c6f774d6574616461746112f4020a21496e73" .
            "74616e7469617465496e6c696e65576f726b666c6f7754656d706c617465" .
            "12422e676f6f676c652e636c6f75642e6461746170726f632e76312e496e" .
            "7374616e7469617465496e6c696e65576f726b666c6f7754656d706c6174" .
            "65526571756573741a1d2e676f6f676c652e6c6f6e6772756e6e696e672e" .
            "4f7065726174696f6e22eb0182d3e49302a60122472f76312f7b70617265" .
            "6e743d70726f6a656374732f2a2f6c6f636174696f6e732f2a7d2f776f72" .
            "6b666c6f7754656d706c617465733a696e7374616e7469617465496e6c69" .
            "6e653a0874656d706c6174655a5122452f76312f7b706172656e743d7072" .
            "6f6a656374732f2a2f726567696f6e732f2a7d2f776f726b666c6f775465" .
            "6d706c617465733a696e7374616e7469617465496e6c696e653a0874656d" .
            "706c617465da410f706172656e742c74656d706c617465ca41290a15676f" .
            "6f676c652e70726f746f6275662e456d7074791210576f726b666c6f774d" .
            "6574616461746112a6020a16557064617465576f726b666c6f7754656d70" .
            "6c61746512372e676f6f676c652e636c6f75642e6461746170726f632e76" .
            "312e557064617465576f726b666c6f7754656d706c617465526571756573" .
            "741a2a2e676f6f676c652e636c6f75642e6461746170726f632e76312e57" .
            "6f726b666c6f7754656d706c61746522a60182d3e4930294011a3e2f7631" .
            "2f7b74656d706c6174652e6e616d653d70726f6a656374732f2a2f6c6f63" .
            "6174696f6e732f2a2f776f726b666c6f7754656d706c617465732f2a7d3a" .
            "0874656d706c6174655a481a3c2f76312f7b74656d706c6174652e6e616d" .
            "653d70726f6a656374732f2a2f726567696f6e732f2a2f776f726b666c6f" .
            "7754656d706c617465732f2a7d3a0874656d706c617465da410874656d70" .
            "6c6174651287020a154c697374576f726b666c6f7754656d706c61746573" .
            "12362e676f6f676c652e636c6f75642e6461746170726f632e76312e4c69" .
            "7374576f726b666c6f7754656d706c61746573526571756573741a372e67" .
            "6f6f676c652e636c6f75642e6461746170726f632e76312e4c697374576f" .
            "726b666c6f7754656d706c61746573526573706f6e7365227d82d3e49302" .
            "6e12352f76312f7b706172656e743d70726f6a656374732f2a2f6c6f6361" .
            "74696f6e732f2a7d2f776f726b666c6f7754656d706c617465735a351233" .
            "2f76312f7b706172656e743d70726f6a656374732f2a2f726567696f6e73" .
            "2f2a7d2f776f726b666c6f7754656d706c61746573da4106706172656e74" .
            "12e6010a1644656c657465576f726b666c6f7754656d706c61746512372e" .
            "676f6f676c652e636c6f75642e6461746170726f632e76312e44656c6574" .
            "65576f726b666c6f7754656d706c617465526571756573741a162e676f6f" .
            "676c652e70726f746f6275662e456d707479227b82d3e493026e2a352f76" .
            "312f7b6e616d653d70726f6a656374732f2a2f6c6f636174696f6e732f2a" .
            "2f776f726b666c6f7754656d706c617465732f2a7d5a352a332f76312f7b" .
            "6e616d653d70726f6a656374732f2a2f726567696f6e732f2a2f776f726b" .
            "666c6f7754656d706c617465732f2a7dda41046e616d651a4bca41176461" .
            "746170726f632e676f6f676c65617069732e636f6dd2412e68747470733a" .
            "2f2f7777772e676f6f676c65617069732e636f6d2f617574682f636c6f75" .
            "642d706c6174666f726d427a0a1c636f6d2e676f6f676c652e636c6f7564" .
            "2e6461746170726f632e76314216576f726b666c6f7754656d706c617465" .
            "7350726f746f50015a40676f6f676c652e676f6c616e672e6f72672f6765" .
            "6e70726f746f2f676f6f676c65617069732f636c6f75642f646174617072" .
            "6f632f76313b6461746170726f63620670726f746f33"
        ), true);

        static::$is_initialized = true;
    }
}

