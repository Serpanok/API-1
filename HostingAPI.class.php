<?php

/**
 * Hosting Ukraine API
 */
class HostingAPI {
	/**
	 * ���� �������
	 * @var string
	 */
	private $key = '';

	/**
	 * ��������� ��������� �� �������� ���������� ��������
	 * @var array
	 */
	private $success = [];

	/**
	 * HostingAPI constructor.
	 * @param string $key
	 */
	public function __construct(string $key) {
		$this->key = $key;
	}

	/**
	 * �������� ��������� ��������� ������� �� �������� ���������� ��������
	 * ���� ����� ����� ������������� ��� �������
	 */
	public function getLastSuccessMessage():array {
		return $this->success;
	}

	/**
	 * ���������� ������ �� NS �������
	 * @param string $domain - �������� ���
	 * @return array
	 */
	public function addDomain(string $domain):int {
		$response = $this->apiCall('action/dns/add_foreign_domain/', ['domain_name' => $domain]);
		return (int)$response['domain_id'];
	}

	/**
	 * ������ �� ������ � API
	 * @param string $action
	 * @param array $post
	 * @return mixed
	 */
	private function apiCall(string $action, array $post) {
		// ���������� ������ �� ������ ������� ����������
		$ch = curl_init("https://adm.tools/".$action);
		curl_setopt_array($ch, [
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array("Authorization: Bearer ".$this->key),
			CURLOPT_POSTFIELDS => $post,
			CURLOPT_VERBOSE => true
		]);
		$json = curl_exec($ch);
		$response = json_decode($json, true);

		// � ������ ������ ����������� ����������
		if ($response['result'] == false) {
			throw new Exception(implode("|", $response['messages']['error']));
		}

		// ��������� ��������� ��������� �� �������� ���������� ��������
		$this->success = $response['messages']['success'] ?? [];

		return $response;
	}
}